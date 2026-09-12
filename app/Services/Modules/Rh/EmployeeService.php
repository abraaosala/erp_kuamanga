<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Employee;
use App\Models\Position;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Support\DepartmentPositionRule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeService implements EmployeeServiceInterface
{
    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepository,
        protected PositionRepositoryInterface $positionRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->employeeRepository->all();
    }

    public function getById(int $id): ?Employee
    {
        return $this->employeeRepository->findById($id);
    }

    public function create(array $data): Employee
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('O nome do funcionário é obrigatório.');
        }

        $positionId = $this->nullableId($data['position_id'] ?? null);
        $departmentId = $this->nullableId($data['department_id'] ?? null);

        $data['department_id'] = DepartmentPositionRule::resolve(
            $departmentId,
            $this->positionDepartmentId($positionId),
        );

        return $this->employeeRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $employee = $this->employeeRepository->findById($id);
        if (!$employee) {
            return false;
        }

        $existingPositionId = $employee->position_id !== null ? (int) $employee->position_id : null;
        $existingDepartmentId = $employee->department_id !== null ? (int) $employee->department_id : null;

        $positionId = array_key_exists('position_id', $data)
            ? $this->nullableId($data['position_id'])
            : $existingPositionId;

        if (array_key_exists('department_id', $data)) {
            $data['department_id'] = DepartmentPositionRule::resolve(
                $this->nullableId($data['department_id']),
                $this->positionDepartmentId($positionId),
            );
        } elseif ($positionId === null) {
            $data['department_id'] = $existingDepartmentId;
        } else {
            $data['department_id'] = DepartmentPositionRule::resolve(
                null,
                $this->positionDepartmentId($positionId),
            );
        }

        return $this->employeeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->employeeRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->employeeRepository->paginate($perPage, $search);
    }

    private function nullableId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function positionDepartmentId(?int $positionId): ?int
    {
        if ($positionId === null) {
            return null;
        }

        $position = $this->positionRepository->findById($positionId);
        if (!$position) {
            throw new \InvalidArgumentException('Cargo não encontrado.');
        }

        return $position->department_id !== null ? (int) $position->department_id : null;
    }
}
