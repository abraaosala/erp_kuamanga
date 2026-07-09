<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeService implements EmployeeServiceInterface
{
    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepository
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

        return $this->employeeRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
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
}
