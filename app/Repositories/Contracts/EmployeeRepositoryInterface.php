<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Employee;

    public function create(array $data): Employee;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
