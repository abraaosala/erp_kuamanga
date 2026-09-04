<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PositionRepositoryInterface
{
    /**
     * @return Collection<int, Position>
     */
    public function all(): Collection;

    public function findById(int $id): ?Position;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Position;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, Position>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @return Collection<int, Position>
     */
    public function findByDepartment(int $departmentId): Collection;
}
