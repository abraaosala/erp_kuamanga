<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Employee;
use App\Models\Rotation;
use App\Models\ScheduledShift;
use App\Models\WorkSchedule;
use App\Repositories\Contracts\EmployeeScheduleRepositoryInterface;
use App\Repositories\Contracts\RosterRepositoryInterface;
use App\Services\Contracts\RosterServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RosterService implements RosterServiceInterface
{
    public function __construct(
        protected RosterRepositoryInterface $rosterRepository,
        protected EmployeeScheduleRepositoryInterface $employeeScheduleRepository,
    ) {}

    /**
     * @return Collection<int, Rotation>
     */
    public function getAllRotations(): Collection
    {
        return $this->rosterRepository->allRotations();
    }

    public function getRotationById(int $id): ?Rotation
    {
        return $this->rosterRepository->findRotationById($id);
    }

    public function paginateRotations(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->rosterRepository->paginateRotations($perPage, $search);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function generate(array $data): Rotation
    {
        /** @var mixed $patternRaw */
        $patternRaw = $data['pattern'] ?? [];
        /** @var list<int> $pattern */
        $pattern = is_array($patternRaw)
            ? array_values(array_map(
                fn(mixed $d): int => $this->toInt($d),
                $patternRaw,
            ))
            : [];

        $startDate  = $this->toString($data['start_date'] ?? '');
        $endDate    = $this->toString($data['end_date'] ?? '');
        $scheduleId = $this->toInt($data['work_schedule_id'] ?? 0);

        $rotation = $this->rosterRepository->createRotation([
            'name'       => $this->toString($data['name'] ?? ''),
            'pattern'    => $pattern,
            'start_date' => $startDate !== '' ? $startDate : null,
            'end_date'   => $endDate !== '' ? $endDate : null,
            'status'     => 'ativo',
        ]);

        $employees = $this->resolveEmployees($data, $scheduleId);

        if ($employees->isNotEmpty() && $startDate !== '' && $endDate !== '' && $pattern !== []) {
            /** @var list<int> $employeeIds */
            $employeeIds = [];
            /** @var Employee $employee */
            foreach ($employees as $employee) {
                $employeeIds[] = $employee->id;
            }

            $this->rosterRepository->deleteGeneratedBetween($startDate, $endDate, $employeeIds);

            $rows = $this->buildShiftRows($rotation, $employees, $scheduleId, $startDate, $endDate, $pattern);
            $this->rosterRepository->insertShifts($rows);
        }

        return $rotation;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function events(string $start, string $end, ?int $rotationId = null): array
    {
        $shifts = $this->rosterRepository->shiftsBetween($start, $end, $rotationId);

        $events = [];
        /** @var ScheduledShift $shift */
        foreach ($shifts as $shift) {
            $events[] = $this->toEvent($shift);
        }

        return $events;
    }

    /**
     * @return array{
     *     today_work: int,
     *     today_off: int,
     *     total_shifts: int,
     *     rotations_count: int,
     *     employees_covered: int
     * }
     */
    public function stats(string $today): array
    {
        return $this->rosterRepository->stats($today);
    }

    public function deleteRotation(int $id): bool
    {
        $rotation = $this->getRotationById($id);
        if (!$rotation) {
            return false;
        }

        $this->rosterRepository->deleteShiftsByRotation($id);

        return $this->rosterRepository->deleteRotation($id);
    }

    /**
     * @param  array<string, mixed>      $data
     * @return Collection<int, Employee>
     */
    protected function resolveEmployees(array $data, int $scheduleId): Collection
    {
        if (!empty($data['employee_ids']) && is_array($data['employee_ids'])) {
            /** @var list<int> $ids */
            $ids = array_values(array_map(
                fn(mixed $v): int => $this->toInt($v),
                $data['employee_ids'],
            ));

            /** @var \App\Models\Empresa $empresa */
            $empresa = current_empresa();

            /** @var Collection<int, Employee> $employees */
            $employees = Employee::where('empresa_id', $empresa->id)
                ->whereIn('id', $ids)
                ->orderBy('name')
                ->get();

            return $employees;
        }

        return $this->employeeScheduleRepository->getEmployeesBySchedule($scheduleId);
    }

    /**
     * @param  Collection<int, Employee>        $employees
     * @param  list<int>                        $pattern
     * @return array<int, array<string, mixed>>
     */
    protected function buildShiftRows(
        Rotation $rotation,
        Collection $employees,
        int $scheduleId,
        string $startDate,
        string $endDate,
        array $pattern,
    ): array {
        $cycleLength = array_sum($pattern);
        if ($cycleLength <= 0) {
            return [];
        }

        $start = new \DateTimeImmutable($startDate);
        $end   = new \DateTimeImmutable($endDate);
        $totalDays = (int) $start->diff($end)->format('%a') + 1;
        $count     = $employees->count();
        $rows      = [];

        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();

        $index = 0;
        /** @var Employee $employee */
        foreach ($employees as $employee) {
            $offset = (int) round($index * $cycleLength / max(1, $count));
            $index++;

            for ($day = 0; $day < $totalDays; $day++) {
                $date = $start->modify("+{$day} days")->format('Y-m-d');
                $pos  = ($day + $offset) % $cycleLength;
                $isWork = $this->isWorkPhase($pos, $pattern);

                $rows[] = [
                    'empresa_id'       => $empresa->id,
                    'employee_id'      => $employee->id,
                    'work_schedule_id' => $isWork ? $scheduleId : null,
                    'rotation_id'      => $rotation->id,
                    'date'             => $date,
                    'classification'   => $isWork ? 'TRABALHO' : 'FOLGA',
                    'source'           => 'GERADA',
                ];
            }
        }

        return $rows;
    }

    /**
     * @param list<int> $pattern
     */
    protected function isWorkPhase(int $position, array $pattern): bool
    {
        $cursor = 0;
        foreach ($pattern as $index => $length) {
            $cursor += $length;
            if ($position < $cursor) {
                return $index % 2 === 0;
            }
        }

        return true;
    }

    protected function toInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '' && ctype_digit($value)) {
            return (int) $value;
        }

        return 0;
    }

    protected function toString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function toEvent(ScheduledShift $shift): array
    {
        /** @var Employee|null $employee */
        $employee = $shift->employee;
        /** @var WorkSchedule|null $schedule */
        $schedule = $shift->workSchedule;
        /** @var Rotation|null $rotation */
        $rotation = $shift->rotation;

        $isWork = $shift->classification === 'TRABALHO';
        $color = $isWork
            ? '#7c3aed'
            : '#64748b';

        return [
            'id'        => (string) $shift->id,
            'title'     => ($employee->name ?? 'Funcionário') . ($isWork && $schedule ? ' · ' . $schedule->name : ''),
            'start'     => $shift->date->format('Y-m-d'),
            'allDay'    => true,
            'scrollable' => false,
            'backgroundColor' => $color,
            'borderColor'     => $color,
            'textColor'       => '#ffffff',
            'extendedProps'   => [
                'classification' => $shift->classification,
                'work_schedule'  => $isWork && $schedule ? $schedule->name : null,
                'check_in'       => $schedule?->check_in_time,
                'check_out'      => $schedule?->check_out_time,
                'rotation'       => $rotation?->name,
                'source'         => $shift->source,
            ],
        ];
    }
}
