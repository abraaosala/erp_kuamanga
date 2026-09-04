<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DepartmentRepositoryInterface
{
    /**
     * @return Collection<int, Department>
     */
    public function all(): Collection;

    public function findById(int $id): ?Department;

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
     * @return LengthAwarePaginator<int, Department>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
