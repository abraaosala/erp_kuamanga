<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PositionServiceInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?Position;

    public function create(array $data): Position;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function getByDepartment(int $departmentId): Collection;
}
