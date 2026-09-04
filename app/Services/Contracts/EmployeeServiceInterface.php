<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?Employee;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Employee;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Employee>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
