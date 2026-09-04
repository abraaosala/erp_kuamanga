<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Employee;
use App\Models\WorkSchedule;
use App\Repositories\Contracts\EmployeeScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeScheduleRepository implements EmployeeScheduleRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployeesBySchedule(int $scheduleId): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employees */
        $employees = Employee::where('empresa_id', $this->empresaId())
            ->whereHas('schedules', function ($q) use ($scheduleId) {
                $q->where('employee_schedules.work_schedule_id', $scheduleId);
            })
            ->with(['department', 'position'])
            ->orderBy('name')
            ->get();

        return $employees;
    }

    /**
     * @return Collection<int, WorkSchedule>
     */
    public function getSchedulesByEmployee(int $employeeId): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkSchedule> $schedules */
        $schedules = WorkSchedule::where('empresa_id', $this->empresaId())
            ->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_schedules.employee_id', $employeeId);
            })
            ->orderBy('name')
            ->get();

        return $schedules;
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function assign(int $employeeId, int $scheduleId, array $meta = []): bool
    {
        if ($this->exists($employeeId, $scheduleId)) {
            return false;
        }

        DB::table('employee_schedules')->insert(array_merge([
            'empresa_id' => $this->empresaId(),
            'employee_id' => $employeeId,
            'work_schedule_id' => $scheduleId,
            'is_default' => $meta['is_default'] ?? false,
            'start_date' => $meta['start_date'] ?? null,
            'end_date' => $meta['end_date'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]));

        return true;
    }

    public function remove(int $employeeId, int $scheduleId): bool
    {
        return (bool) DB::table('employee_schedules')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->where('work_schedule_id', $scheduleId)
            ->delete();
    }

    public function setDefault(int $employeeId, int $scheduleId): bool
    {
        DB::table('employee_schedules')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->update(['is_default' => false]);

        $updated = DB::table('employee_schedules')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->where('work_schedule_id', $scheduleId)
            ->update(['is_default' => true]);

        return (bool) $updated;
    }

    public function exists(int $employeeId, int $scheduleId): bool
    {
        return DB::table('employee_schedules')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->where('work_schedule_id', $scheduleId)
            ->exists();
    }
}
