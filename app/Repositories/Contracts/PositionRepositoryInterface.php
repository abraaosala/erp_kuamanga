<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PositionRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Position;

    public function create(array $data): Position;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function findByDepartment(int $departmentId): Collection;
}
