<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DepartmentServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?Department;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Department;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Department>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
