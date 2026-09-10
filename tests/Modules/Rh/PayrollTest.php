<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Repositories\Modules\Rh\AttendanceRepository;
use App\Repositories\Modules\Rh\ContractRepository;
use App\Repositories\Modules\Rh\EmployeeRepository;
use App\Repositories\Modules\Rh\HourBankEntryRepository;
use App\Repositories\Modules\Rh\PayrollRepository;
use App\Services\Modules\Rh\PayrollService;

beforeEach(function (): void {
    $this->empresa = $this->createEmpresa();
    $this->employee = $this->createEmployee($this->empresa->id);

    $_SESSION['empresa_id'] = $this->empresa->id;

    $this->payrollRepo = new PayrollRepository();
    $this->contractRepo = new ContractRepository();
    $this->hourBankRepo = new HourBankEntryRepository();
    $this->attendanceRepo = new AttendanceRepository();
    $this->employeeRepo = new EmployeeRepository();

    $this->service = new PayrollService(
        $this->payrollRepo,
        $this->employeeRepo,
        $this->contractRepo,
        $this->hourBankRepo,
        $this->attendanceRepo
    );
});

it('generates a payroll run from active contract', function (): void {
    Contract::create([
        'empresa_id'   => $this->empresa->id,
        'employee_id'  => $this->employee->id,
        'tipo_contrato'=> 'tempo_integral',
        'data_inicio'  => '2026-01-01',
        'salario_base' => 300000.00,
        'status'       => 'active',
    ]);

    $result = $this->service->runFromContracts('2026-08-01', '2026-08-31', 'Folha de Agosto');

    expect($result['run'])->not->toBeNull();
    expect($result['run']->employee_count)->toBe(1);
    expect((float) $result['run']->total_gross)->toBe(300000.0);

    $payslips = $result['payslips'];
    expect($payslips)->toHaveCount(1);
    expect((float) $payslips->first()->base_salary)->toBe(300000.0);
    expect((float) $payslips->first()->net_salary)->toBe(300000.0);
});

it('skips employees without active contract', function (): void {
    Contract::create([
        'empresa_id'   => $this->empresa->id,
        'employee_id'  => $this->employee->id,
        'tipo_contrato'=> 'termo_certo',
        'data_inicio'  => '2026-01-01',
        'data_fim'     => '2026-03-31',
        'salario_base' => 150000.00,
        'status'       => 'inactive',
    ]);

    expect(fn () => $this->service->runFromContracts('2026-08-01', '2026-08-31', 'Folha de Agosto'))
        ->toThrow(\RuntimeException::class);

    expect(\App\Models\PayrollRun::count())->toBe(0);
});

it('throws when there are no employees with eligible contracts', function (): void {
    expect(fn () => $this->service->runFromContracts('2026-08-01', '2026-08-31', 'Folha de Agosto'))
        ->toThrow(\RuntimeException::class, 'contrato activo elegível');

    expect(\App\Models\PayrollRun::count())->toBe(0);
});

it('adds overtime from hour bank to gross salary', function (): void {
    Contract::create([
        'empresa_id'   => $this->empresa->id,
        'employee_id'  => $this->employee->id,
        'tipo_contrato'=> 'tempo_integral',
        'data_inicio'  => '2026-01-01',
        'salario_base' => 300000.00,
        'status'       => 'active',
    ]);

    \App\Models\HourBankEntry::create([
        'empresa_id'  => $this->empresa->id,
        'employee_id' => $this->employee->id,
        'date'        => '2026-08-15',
        'hours'       => 10.00,
        'type'        => 'horas_extra',
    ]);

    $result = $this->service->runFromContracts('2026-08-01', '2026-08-31', 'Folha de Agosto');
    $payslip = $result['payslips']->first();

    $hourlyRate = 300000 / 173.33;
    $expectedOvertime = 10 * $hourlyRate * 1.5;

    expect((float) $payslip->overtime_hours)->toBe(10.0);
    expect((float) $payslip->overtime_amount)->toBe((float) round($expectedOvertime, 2));
    expect((float) $payslip->gross_salary)->toBe((float) round(300000 + $expectedOvertime, 2));
});

it('deducts absent days from net salary', function (): void {
    Contract::create([
        'empresa_id'   => $this->empresa->id,
        'employee_id'  => $this->employee->id,
        'tipo_contrato'=> 'tempo_integral',
        'data_inicio'  => '2026-01-01',
        'salario_base' => 300000.00,
        'status'       => 'active',
    ]);

    \App\Models\Attendance::create([
        'empresa_id'  => $this->empresa->id,
        'employee_id' => $this->employee->id,
        'date'        => '2026-08-10',
        'status'      => 'falta',
    ]);
    \App\Models\Attendance::create([
        'empresa_id'  => $this->empresa->id,
        'employee_id' => $this->employee->id,
        'date'        => '2026-08-11',
        'status'      => 'falta',
    ]);

    $result = $this->service->runFromContracts('2026-08-01', '2026-08-31', 'Folha de Agosto');
    $payslip = $result['payslips']->first();

    $dailyRate = 300000 / 30;
    $expectedDeduction = 2 * $dailyRate;

    expect((float) $payslip->absent_days)->toBe(2.0);
    expect((float) $payslip->absent_deduction)->toBe((float) round($expectedDeduction, 2));
    expect((float) $payslip->net_salary)->toBe((float) round(300000 - $expectedDeduction, 2));
});

it('totals match across paid employees', function (): void {
    $employee2 = $this->createEmployee($this->empresa->id);

    Contract::create([
        'empresa_id'   => $this->empresa->id,
        'employee_id'  => $this->employee->id,
        'tipo_contrato'=> 'tempo_integral',
        'data_inicio'  => '2026-01-01',
        'salario_base' => 200000.00,
        'status'       => 'active',
    ]);
    Contract::create([
        'empresa_id'   => $this->empresa->id,
        'employee_id'  => $employee2->id,
        'tipo_contrato'=> 'tempo_integral',
        'data_inicio'  => '2026-01-01',
        'salario_base' => 300000.00,
        'status'       => 'active',
    ]);

    $result = $this->service->runFromContracts('2026-08-01', '2026-08-31', 'Folha de Agosto');

    expect($result['run']->employee_count)->toBe(2);
    expect((float) $result['run']->total_gross)->toBe(500000.0);
    expect((float) $result['run']->total_net)->toBe(500000.0);
    expect($result['payslips'])->toHaveCount(2);
});

it('deletes payslips when run is removed', function (): void {
    Contract::create([
        'empresa_id'   => $this->empresa->id,
        'employee_id'  => $this->employee->id,
        'tipo_contrato'=> 'tempo_integral',
        'data_inicio'  => '2026-01-01',
        'salario_base' => 200000.00,
        'status'       => 'active',
    ]);

    $result = $this->service->runFromContracts('2026-08-01', '2026-08-31', 'Folha de Agosto');
    $runId = (int) $result['run']->id;

    $this->service->delete($runId);

    expect(\App\Models\PayrollRun::find($runId))->toBeNull();
    expect(\App\Models\Payslip::where('payroll_run_id', $runId)->count())->toBe(0);
});