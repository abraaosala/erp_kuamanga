<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\ScheduledShift;
use App\Repositories\Modules\Rh\EmployeeScheduleRepository;
use App\Repositories\Modules\Rh\RosterRepository;
use App\Services\Modules\Rh\EmployeeScheduleService;
use App\Services\Modules\Rh\RosterService;

beforeEach(function (): void {
    $this->empresa = $this->createEmpresa();
    $this->schedule = $this->createSchedule($this->empresa->id);

    $this->employee1 = $this->createEmployee($this->empresa->id);
    $this->employee1->update(['name' => 'Ana Silva']);
    $this->employee2 = $this->createEmployee($this->empresa->id);
    $this->employee2->update(['name' => 'Bruno Dias']);
    $this->employee3 = $this->createEmployee($this->empresa->id);
    $this->employee3->update(['name' => 'Carla Mendes']);

    $_SESSION['empresa_id'] = $this->empresa->id;

    $this->rosterRepo = new RosterRepository();
    $this->employeeScheduleService = new EmployeeScheduleService(new EmployeeScheduleRepository());
    $this->service = new RosterService($this->rosterRepo, new EmployeeScheduleRepository());
});

it('creates a rotation and generates shifts for the team', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);
    $this->employeeScheduleService->assign($this->employee2->id, $this->schedule->id);
    $this->employeeScheduleService->assign($this->employee3->id, $this->schedule->id);

    $rotation = $this->service->generate([
        'name'              => 'Portaria 6x2',
        'pattern'           => [6, 2],
        'start_date'        => '2026-09-01',
        'end_date'          => '2026-09-08',
        'work_schedule_id'  => $this->schedule->id,
    ]);

    expect($rotation->id)->toBeInt();
    expect($rotation->name)->toBe('Portaria 6x2');

    $count = ScheduledShift::where('empresa_id', $this->empresa->id)
        ->where('rotation_id', $rotation->id)
        ->count();

    expect($count)->toBe(3 * 8);

    $workDays = ScheduledShift::where('empresa_id', $this->empresa->id)
        ->where('rotation_id', $rotation->id)
        ->where('classification', 'TRABALHO')
        ->count();

    $offDays = ScheduledShift::where('empresa_id', $this->empresa->id)
        ->where('rotation_id', $rotation->id)
        ->where('classification', 'FOLGA')
        ->count();

    expect($workDays)->toBe(3 * 6);
    expect($offDays)->toBe(3 * 2);
});

it('staggering ensures no day is uncovered in a 2x2 with two employees', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);
    $this->employeeScheduleService->assign($this->employee2->id, $this->schedule->id);

    $rotation = $this->service->generate([
        'name'             => 'Dupla 2x2',
        'pattern'          => [2, 2],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-04',
        'work_schedule_id' => $this->schedule->id,
    ]);

    $daily = ScheduledShift::where('empresa_id', $this->empresa->id)
        ->where('rotation_id', $rotation->id)
        ->where('classification', 'TRABALHO')
        ->get()
        ->groupBy('date');

    expect($daily)->toHaveCount(4);
    expect(array_values($daily->map->count()->all()))->toBe([1, 1, 1, 1]);
});

it('events returns calendar-ready payload', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);

    $rotation = $this->service->generate([
        'name'             => 'Turno Único',
        'pattern'          => [1, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-02',
        'work_schedule_id' => $this->schedule->id,
    ]);

    $events = $this->service->events('2026-09-01', '2026-09-30');

    expect($events)->toHaveCount(2);
    expect($events[0]['id'])->toBeString();
    expect($events[0]['allDay'])->toBeTrue();
    expect($events[0]['extendedProps']['rotation'])->toBe('Turno Único');
    expect($events[0]['extendedProps']['work_schedule'])->toBe($this->schedule->name);
});

it('events filters calendar payload by rotation', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);
    $this->employeeScheduleService->assign($this->employee2->id, $this->schedule->id);

    $first = $this->service->generate([
        'name'             => 'Turno A',
        'pattern'          => [1, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-02',
        'work_schedule_id' => $this->schedule->id,
    ]);

    $second = $this->service->generate([
        'name'             => 'Turno B',
        'pattern'          => [1, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-02',
        'work_schedule_id' => $this->schedule->id,
        'employee_ids'     => [$this->employee2->id],
    ]);

    $filtered = $this->service->events('2026-09-01', '2026-09-30', $second->id);

    expect($filtered)->toHaveCount(2);
    foreach ($filtered as $event) {
        expect($event['extendedProps']['rotation'])->toBe('Turno B');
    }
});

it('stats aggregates today, totals and employee coverage', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);
    $this->employeeScheduleService->assign($this->employee2->id, $this->schedule->id);

    $this->service->generate([
        'name'             => 'Estatísticas',
        'pattern'          => [1, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-04',
        'work_schedule_id' => $this->schedule->id,
    ]);

    $stats = $this->service->stats('2026-09-02');

    // Stagger: no ciclo 1x1 com 2 funcionários, metade trabalha por dia
    expect($stats['today_work'])->toBe(1);
    expect($stats['today_off'])->toBe(1);
    expect($stats['total_shifts'])->toBe(2 * 4);
    expect($stats['rotations_count'])->toBe(1);
    expect($stats['employees_covered'])->toBe(2);
});

it('regenerating a rotation does not duplicate unique employee+date rows', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);

    $data = [
        'name'             => 'Recorrente',
        'pattern'          => [1, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-03',
        'work_schedule_id' => $this->schedule->id,
    ];

    $this->service->generate($data);
    $this->service->generate($data);

    $count = ScheduledShift::where('empresa_id', $this->empresa->id)
        ->where('date', '>=', '2026-09-01')
        ->where('date', '<=', '2026-09-03')
        ->count();

    expect($count)->toBe(3);
});

it('two rotations in the same period keep their own generated shifts', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);
    $this->employeeScheduleService->assign($this->employee2->id, $this->schedule->id);

    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id, ['is_default' => true]);
    $this->employeeScheduleService->assign($this->employee2->id, $this->schedule->id, ['is_default' => true]);

    $first = $this->service->generate([
        'name'             => 'Equipa A',
        'pattern'          => [1, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-03',
        'work_schedule_id' => $this->schedule->id,
    ]);

    // Simulate a second team (employee2) generated for the same period
    $second = $this->service->generate([
        'name'             => 'Equipa B',
        'pattern'          => [1, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-03',
        'work_schedule_id' => $this->schedule->id,
        'employee_ids'     => [$this->employee2->id],
    ]);

    $shiftsForEmployee1 = ScheduledShift::where('rotation_id', $first->id)->count();
    $shiftsForEmployee2 = ScheduledShift::where('rotation_id', $second->id)->count();

    expect($shiftsForEmployee1)->toBe(3);
    expect($shiftsForEmployee2)->toBe(3);
});

it('deletes a rotation and its generated shifts', function (): void {
    $this->employeeScheduleService->assign($this->employee1->id, $this->schedule->id);

    $rotation = $this->service->generate([
        'name'             => 'Remover',
        'pattern'          => [2, 1],
        'start_date'       => '2026-09-01',
        'end_date'         => '2026-09-03',
        'work_schedule_id' => $this->schedule->id,
    ]);

    $ok = $this->service->deleteRotation($rotation->id);

    expect($ok)->toBeTrue();
    expect(ScheduledShift::where('rotation_id', $rotation->id)->count())->toBe(0);
    expect(\App\Models\Rotation::find($rotation->id))->toBeNull();
});