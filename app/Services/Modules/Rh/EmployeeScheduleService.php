<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Employee;
use App\Models\WorkSchedule;
use App\Repositories\Contracts\EmployeeScheduleRepositoryInterface;
use App\Services\Contracts\EmployeeScheduleServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class EmployeeScheduleService implements EmployeeScheduleServiceInterface
{
    public function __construct(
        protected EmployeeScheduleRepositoryInterface $employeeScheduleRepository
    ) {}

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployeesBySchedule(int $scheduleId): Collection
    {
        return $this->employeeScheduleRepository->getEmployeesBySchedule($scheduleId);
    }

    /**
     * @return Collection<int, WorkSchedule>
     */
    public function getSchedulesByEmployee(int $employeeId): Collection
    {
        return $this->employeeScheduleRepository->getSchedulesByEmployee($employeeId);
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function assign(int $employeeId, int $scheduleId, array $meta = []): bool
    {
        return $this->employeeScheduleRepository->assign($employeeId, $scheduleId, $meta);
    }

    public function remove(int $employeeId, int $scheduleId): bool
    {
        return $this->employeeScheduleRepository->remove($employeeId, $scheduleId);
    }

    public function setDefault(int $employeeId, int $scheduleId): bool
    {
        return $this->employeeScheduleRepository->setDefault($employeeId, $scheduleId);
    }

    public function exists(int $employeeId, int $scheduleId): bool
    {
        return $this->employeeScheduleRepository->exists($employeeId, $scheduleId);
    }
}
