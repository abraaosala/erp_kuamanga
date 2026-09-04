<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\WorkSchedule;
use App\Repositories\Modules\Rh\EmployeeScheduleRepository;
use App\Services\Modules\Rh\EmployeeScheduleService;

beforeEach(function (): void {
    $this->empresa = $this->createEmpresa();
    $this->employee = $this->createEmployee($this->empresa->id);
    $this->schedule = $this->createSchedule($this->empresa->id);

    $_SESSION['empresa_id'] = $this->empresa->id;

    $this->repo = new EmployeeScheduleRepository();
    $this->service = new EmployeeScheduleService($this->repo);
});

it('assigns employee to schedule', function (): void {
    $result = $this->service->assign($this->employee->id, $this->schedule->id);

    expect($result)->toBeTrue();

    $employees = $this->service->getEmployeesBySchedule($this->schedule->id);
    expect($employees)->toHaveCount(1);
    expect($employees->first()->id)->toBe($this->employee->id);
});

it('prevents duplicate assignment', function (): void {
    $this->service->assign($this->employee->id, $this->schedule->id);
    $result = $this->service->assign($this->employee->id, $this->schedule->id);

    expect($result)->toBeFalse();

    $employees = $this->service->getEmployeesBySchedule($this->schedule->id);
    expect($employees)->toHaveCount(1);
});

it('removes employee from schedule', function (): void {
    $this->service->assign($this->employee->id, $this->schedule->id);
    $result = $this->service->remove($this->employee->id, $this->schedule->id);

    expect($result)->toBeTrue();

    $employees = $this->service->getEmployeesBySchedule($this->schedule->id);
    expect($employees)->toHaveCount(0);
});

it('sets default schedule for employee', function (): void {
    $schedule2 = $this->createSchedule($this->empresa->id);
    $schedule2->update(['name' => 'Turno Noturno']);

    $this->service->assign($this->employee->id, $this->schedule->id, ['is_default' => true]);
    $this->service->assign($this->employee->id, $schedule2->id);

    $schedules = $this->service->getSchedulesByEmployee($this->employee->id);
    expect($schedules)->toHaveCount(2);

    $result = $this->service->setDefault($this->employee->id, $schedule2->id);
    expect($result)->toBeTrue();
});

it('gets schedules by employee', function (): void {
    $schedule2 = $this->createSchedule($this->empresa->id);
    $schedule2->update(['name' => 'Turno Noturno']);

    $this->service->assign($this->employee->id, $this->schedule->id);
    $this->service->assign($this->employee->id, $schedule2->id);

    $schedules = $this->service->getSchedulesByEmployee($this->employee->id);
    expect($schedules)->toHaveCount(2);
});

it('gets employees by schedule', function (): void {
    $employee2 = $this->createEmployee($this->empresa->id);
    $employee2->update(['name' => 'Funcionário 2']);

    $this->service->assign($this->employee->id, $this->schedule->id);
    $this->service->assign($employee2->id, $this->schedule->id);

    $employees = $this->service->getEmployeesBySchedule($this->schedule->id);
    expect($employees)->toHaveCount(2);
});

it('checks if assignment exists', function (): void {
    expect($this->service->exists($this->employee->id, $this->schedule->id))->toBeFalse();

    $this->service->assign($this->employee->id, $this->schedule->id);

    expect($this->service->exists($this->employee->id, $this->schedule->id))->toBeTrue();
});

it('assigns with meta data', function (): void {
    $this->service->assign($this->employee->id, $this->schedule->id, [
        'is_default' => true,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    $schedules = $this->service->getSchedulesByEmployee($this->employee->id);
    expect($schedules)->toHaveCount(1);

    $pivot = $this->employee->schedules()->first()->pivot;
    expect((bool) $pivot->is_default)->toBeTrue();
    expect($pivot->start_date)->toBe('2026-01-01');
    expect($pivot->end_date)->toBe('2026-12-31');
});

it('scopes by empresa', function (): void {
    $otherEmpresa = $this->createEmpresa();
    $otherEmployee = \App\Models\Employee::create([
        'empresa_id' => $otherEmpresa->id,
        'name' => 'Outro Funcionário',
    ]);

    $this->service->assign($this->employee->id, $this->schedule->id);

    $employees = $this->service->getEmployeesBySchedule($this->schedule->id);
    expect($employees)->toHaveCount(1);
    expect($employees->first()->id)->toBe($this->employee->id);
});
