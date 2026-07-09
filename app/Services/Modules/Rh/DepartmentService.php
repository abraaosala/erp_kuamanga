<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Department;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Services\Contracts\DepartmentServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DepartmentService implements DepartmentServiceInterface
{
    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->departmentRepository->all();
    }

    public function getById(int $id): ?Department
    {
        return $this->departmentRepository->findById($id);
    }

    public function create(array $data): Department
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('O nome do departamento é obrigatório.');
        }

        return $this->departmentRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->departmentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->departmentRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->departmentRepository->paginate($perPage, $search);
    }
}
