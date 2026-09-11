<?php

declare(strict_types=1);

use App\Models\Benefit;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Repositories\Modules\Rh\BenefitRepository;
use App\Repositories\Modules\Rh\EmployeeRepository;
use App\Services\Modules\Rh\BenefitPolicy;
use App\Services\Modules\Rh\BenefitService;

beforeEach(function (): void {
    $this->empresa = $this->createEmpresa();
    $_SESSION['empresa_id'] = $this->empresa->id;

    $this->repo = new BenefitRepository();
    $this->service = new BenefitService($this->repo, new EmployeeRepository());

    $this->department = Department::create([
        'empresa_id' => $this->empresa->id,
        'name' => 'Operações',
        'status' => 'active',
    ]);

    $this->position = Position::create([
        'empresa_id' => $this->empresa->id,
        'department_id' => $this->department->id,
        'name' => 'Operador',
        'status' => 'active',
    ]);

    $this->employee = $this->createEmployee($this->empresa->id);
    $this->employee->update([
        'hire_date' => date('Y-m-d', strtotime('-24 months')),
    ]);
});

it('creates a benefit with defaults', function (): void {
    $benefit = $this->service->create([
        'name' => 'Seguro de Saúde',
        'category' => 'saude',
    ]);

    expect($benefit->status)->toBe(Benefit::STATUS_ACTIVE);
    expect($benefit->min_tenure_months)->toBe(0);
    expect(Benefit::find($benefit->id)->name)->toBe('Seguro de Saúde');
});

it('updates a benefit', function (): void {
    $benefit = $this->service->create(['name' => 'Subsídio de Transporte']);

    $this->service->update((int) $benefit->id, ['name' => 'Transporte Reforçado']);

    expect($this->service->getById((int) $benefit->id)->name)->toBe('Transporte Reforçado');
});

it('soft deletes a benefit', function (): void {
    $benefit = $this->service->create(['name' => 'Bónus Anual']);

    $this->service->delete((int) $benefit->id);

    expect($this->service->getById((int) $benefit->id))->toBeNull();
    expect(Benefit::withTrashed()->find($benefit->id))->not->toBeNull();
});

it('considers an employee eligible when there is no restriction', function (): void {
    $benefit = $this->service->create(['name' => 'Kit Escolar']);
    $policy = new BenefitPolicy();

    expect($policy->isEligible($this->employee, $benefit))->toBeTrue();
});

it('blocks eligibility when restricted to another position', function (): void {
    $benefit = $this->service->create([
        'name' => 'Subsídio de Carga',
        'position_id' => $this->position->id,
    ]);
    $policy = new BenefitPolicy();

    expect($policy->isEligible($this->employee, $benefit))->toBeFalse();

    $this->employee->update(['position_id' => $this->position->id]);
    expect($policy->isEligible($this->employee, $benefit))->toBeTrue();
});

it('blocks eligibility when restricted to another department', function (): void {
    $benefit = $this->service->create([
        'name' => 'Seguro Operacional',
        'department_id' => $this->department->id,
    ]);
    $policy = new BenefitPolicy();

    expect($policy->isEligible($this->employee, $benefit))->toBeFalse();

    $this->employee->update(['department_id' => $this->department->id]);
    expect($policy->isEligible($this->employee, $benefit))->toBeTrue();
});

it('blocks eligibility when minimum tenure is not met', function (): void {
    $benefit = $this->service->create([
        'name' => 'Carreira Longa',
        'min_tenure_months' => 12,
    ]);
    $policy = new BenefitPolicy();

    $this->employee->update(['hire_date' => date('Y-m-d', strtotime('-6 months'))]);
    expect($policy->isEligible($this->employee, $benefit))->toBeFalse();

    $this->employee->update(['hire_date' => date('Y-m-d', strtotime('-24 months'))]);
    expect($policy->isEligible($this->employee, $benefit))->toBeTrue();
});

it('explains why an employee is ineligible', function (): void {
    $benefit = $this->service->create([
        'name' => 'Restrito',
        'position_id' => $this->position->id,
        'department_id' => $this->department->id,
        'min_tenure_months' => 12,
    ]);
    $this->employee->update(['hire_date' => date('Y-m-d', strtotime('-6 months'))]);
    $policy = new BenefitPolicy();

    $violations = $policy->violations($this->employee, $benefit);

    expect($violations)->toHaveCount(3);
    expect($violations)->toContain('Benefício restrito a outro cargo.');
});

it('assigns the benefit to an eligible employee', function (): void {
    $benefit = $this->service->create(['name' => 'Vale Refeição']);

    $this->service->assign((int) $benefit->id, (int) $this->employee->id, date('Y-m-d'));

    expect($this->repo->existsAssignment((int) $benefit->id, (int) $this->employee->id))->toBeTrue();
});

it('rejects assignment to an ineligible employee', function (): void {
    $benefit = $this->service->create([
        'name' => 'Subsídio de Carga',
        'position_id' => $this->position->id,
    ]);

    expect(fn () => $this->service->assign((int) $benefit->id, (int) $this->employee->id))
        ->toThrow(\RuntimeException::class, 'não elegível');
});

it('rejects a duplicate assignment', function (): void {
    $benefit = $this->service->create(['name' => 'Ginásio']);
    $this->service->assign((int) $benefit->id, (int) $this->employee->id);

    expect(fn () => $this->service->assign((int) $benefit->id, (int) $this->employee->id))
        ->toThrow(\RuntimeException::class, 'já possui');
});

it('unassigns the benefit from an employee', function (): void {
    $benefit = $this->service->create(['name' => 'Telemóvel']);
    $this->service->assign((int) $benefit->id, (int) $this->employee->id);

    $this->service->unassign((int) $benefit->id, (int) $this->employee->id);

    expect($this->repo->existsAssignment((int) $benefit->id, (int) $this->employee->id))->toBeFalse();
});

it('lists employees by benefit', function (): void {
    $benefit = $this->service->create(['name' => 'Alimentação']);
    $this->service->assign((int) $benefit->id, (int) $this->employee->id);

    $employees = $this->service->employeesByBenefit((int) $benefit->id);

    expect($employees)->toHaveCount(1);
    expect($employees->first()->id)->toBe($this->employee->id);
});

it('filters eligible employees and excludes those already assigned', function (): void {
    $benefit = $this->service->create(['name' => 'Seguro de Vida']);
    $other = $this->createEmployee($this->empresa->id);
    $other->update(['hire_date' => date('Y-m-d', strtotime('-36 months'))]);

    $this->service->assign((int) $benefit->id, (int) $this->employee->id);

    $eligible = $this->service->eligibleEmployees((int) $benefit->id);

    expect($eligible)->toHaveCount(1);
    expect($eligible->first()->id)->toBe($other->id);
});

it('computes the summary with totals and assignments', function (): void {
    $benefit = $this->service->create(['name' => 'Bónus']);
    $inactive = $this->service->create(['name' => 'Antigo', 'status' => Benefit::STATUS_INACTIVE]);
    $this->service->assign((int) $benefit->id, (int) $this->employee->id);

    $summary = $this->service->summary();

    expect($summary['total'])->toBe(2);
    expect($summary['active'])->toBe(1);
    expect($summary['assignments'])->toBe(1);

    expect((int) $inactive->id)->toBeGreaterThan(0);
});

it('exposes the assigned count by benefit', function (): void {
    $benefit = $this->service->create(['name' => 'Telemóvel']);
    $this->service->assign((int) $benefit->id, (int) $this->employee->id);

    $counts = $this->service->assignedCounts();

    expect($counts[(int) $benefit->id])->toBe(1);
});