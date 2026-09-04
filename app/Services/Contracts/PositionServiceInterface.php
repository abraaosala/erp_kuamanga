<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PositionServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?Position;

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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Position>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position>
     */
    public function getByDepartment(int $departmentId): Collection;
}
