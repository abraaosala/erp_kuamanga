<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Employee;
use App\Models\WorkSchedule;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeScheduleRepositoryInterface
{
    /**
     * @return Collection<int, Employee>
     */
    public function getEmployeesBySchedule(int $scheduleId): Collection;

    /**
     * @return Collection<int, WorkSchedule>
     */
    public function getSchedulesByEmployee(int $employeeId): Collection;

    /**
     * @param array<string, mixed> $meta
     */
    public function assign(int $employeeId, int $scheduleId, array $meta = []): bool;

    public function remove(int $employeeId, int $scheduleId): bool;

    public function setDefault(int $employeeId, int $scheduleId): bool;

    public function exists(int $employeeId, int $scheduleId): bool;
}
