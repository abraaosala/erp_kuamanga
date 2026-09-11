<?php

declare(strict_types=1);

use App\Models\Leave;
use App\Models\LeaveRequest;
use App\Repositories\Modules\Rh\EmployeeRepository;
use App\Repositories\Modules\Rh\LeaveRepository;
use App\Services\Modules\Rh\LeavePolicy;
use App\Services\Modules\Rh\LeaveService;

beforeEach(function (): void {
    $this->empresa = $this->createEmpresa();
    $this->employee = $this->createEmployee($this->empresa->id);
    $this->employee->update(['hire_date' => '2025-09-01']);

    $_SESSION['empresa_id'] = $this->empresa->id;

    $this->leaveRepo = new LeaveRepository();
    $this->service = new LeaveService($this->leaveRepo, new EmployeeRepository());
});

it('entitles 22 working days after one full year of service', function (): void {
    $policy = new LeavePolicy();

    expect($policy->entitledDaysFor('2025-09-01', '2026-09-10'))->toBe(22);
});

it('entitles proportionally in the first year (2 days per complete month)', function (): void {
    $policy = new LeavePolicy();

    expect($policy->entitledDaysFor('2026-03-01', '2026-09-10'))->toBe(12);
});

it('applies the legal minimum of 6 days in the first year', function (): void {
    $policy = new LeavePolicy();

    expect($policy->entitledDaysFor('2026-07-01', '2026-09-10'))->toBe(6);
});

it('grants no entitlement before any complete month', function (): void {
    $policy = new LeavePolicy();

    expect($policy->entitledDaysFor('2026-09-10', '2026-09-10'))->toBe(0);
});

it('counts leave days inclusively', function (): void {
    $policy = new LeavePolicy();

    expect($policy->inclusiveDays('2026-08-10', '2026-08-12'))->toBe(3);
});

it('rejects an invalid leave period', function (): void {
    $policy = new LeavePolicy();

    expect(fn () => $policy->inclusiveDays('2026-08-12', '2026-08-10'))
        ->toThrow(\InvalidArgumentException::class);
});

it('creates a pending leave request with computed days', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-18',
        'reason'      => 'Férias anuais',
    ]);

    expect($request->status)->toBe(LeaveRequest::STATUS_PENDENTE);
    expect($request->days)->toBe(4);
    expect($request->employee_id)->toBe($this->employee->id);
    expect(LeaveRequest::count())->toBe(1);
});

it('approves a pending request and registers the leave', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-18',
    ]);

    $approved = $this->service->approve((int) $request->id, 5, 'Autorizado');

    expect($approved->status)->toBe(LeaveRequest::STATUS_APROVADO);
    expect($approved->decided_by)->toBe(5);
    expect($approved->decided_at)->not->toBeNull();
    expect(Leave::count())->toBe(1);

    $leave = Leave::first();
    expect($leave->leave_request_id)->toBe($request->id);
    expect($leave->status)->toBe(Leave::STATUS_GOZADA);
    expect($leave->days)->toBe(4);
});

it('rejects a pending request without registering a leave', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-18',
    ]);

    $rejected = $this->service->reject((int) $request->id, 5, 'Não autorizado');

    expect($rejected->status)->toBe(LeaveRequest::STATUS_REJEITADO);
    expect($rejected->decision_notes)->toBe('Não autorizado');
    expect(Leave::count())->toBe(0);
});

it('cancels a pending request', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-18',
    ]);

    $cancelled = $this->service->cancel((int) $request->id);

    expect($cancelled->status)->toBe(LeaveRequest::STATUS_CANCELADO);
    expect(Leave::count())->toBe(0);
});

it('prevents approving a request that is no longer pending', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-18',
    ]);

    $this->service->approve((int) $request->id, 5);

    expect(fn () => $this->service->approve((int) $request->id, 5))
        ->toThrow(\RuntimeException::class, 'pedidos pendentes');
    expect(Leave::count())->toBe(1);
});

it('blocks a holiday request beyond the available balance', function (): void {
    $this->employee->update(['hire_date' => '2026-08-01']);

    expect(fn () => $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-10-01',
        'end_date'    => '2026-10-07',
    ]))->toThrow(\RuntimeException::class, 'Saldo de férias insuficiente');
});

it('does not apply the holiday balance to other leave types', function (): void {
    $this->employee->update(['hire_date' => '2026-08-01']);

    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_LICENCA_MATERNIDADE,
        'start_date'  => '2026-10-01',
        'end_date'    => '2026-10-20',
    ]);

    $this->service->approve((int) $request->id, 5);

    expect($request->days)->toBe(20);
    expect($this->service->balanceByEmployee($this->employee->id)['used'])->toBe(0);
});

it('prevents a request that overlaps an approved leave', function (): void {
    $first = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-16',
    ]);
    $this->service->approve((int) $first->id, 5);

    expect(fn () => $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-16',
        'end_date'    => '2026-09-17',
    ]))->toThrow(\RuntimeException::class, 'sobrepõe');
});

it('computes the holiday balance as entitled minus used', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-18',
    ]);
    $this->service->approve((int) $request->id, 5);

    $balance = $this->service->balanceByEmployee($this->employee->id);

    expect($balance['entitled'])->toBe(22);
    expect($balance['used'])->toBe(4);
    expect($balance['available'])->toBe(18);
});

it('summarizes balances for all employees of the company', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-16',
    ]);
    $this->service->approve((int) $request->id, 5);

    $balances = $this->service->balances();

    expect($balances)->toHaveCount(1);
    expect($balances[0]['employee'])->toBe('Funcionário Teste');
    expect($balances[0]['used'])->toBe(2);
});

it('scopes requests and pending count by company', function (): void {
    $empresaB = $this->createEmpresa();
    $employeeB = $this->createEmployee($empresaB->id);
    $employeeB->update(['hire_date' => '2025-09-01']);

    $_SESSION['empresa_id'] = $empresaB->id;
    $serviceB = new LeaveService(new LeaveRepository(), new EmployeeRepository());

    $serviceB->requestLeave([
        'employee_id' => $employeeB->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-20',
        'end_date'    => '2026-09-22',
    ]);

    expect($serviceB->countPending())->toBe(1);
    expect(LeaveRequest::where('empresa_id', $empresaB->id)->count())->toBe(1);
    expect(LeaveRequest::where('empresa_id', $this->empresa->id)->count())->toBe(0);
});

it('removes the registered leave when the request is deleted', function (): void {
    $request = $this->service->requestLeave([
        'employee_id' => $this->employee->id,
        'leave_type'  => LeaveRequest::TYPE_FERIAS,
        'start_date'  => '2026-09-15',
        'end_date'    => '2026-09-18',
    ]);
    $this->service->approve((int) $request->id, 5);

    expect($this->service->delete((int) $request->id))->toBeTrue();
    expect(LeaveRequest::find($request->id))->toBeNull();
    expect(Leave::where('leave_request_id', $request->id)->count())->toBe(0);
});