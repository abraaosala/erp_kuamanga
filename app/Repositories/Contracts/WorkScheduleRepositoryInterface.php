<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\WorkSchedule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WorkScheduleRepositoryInterface
{
    /**
     * @return Collection<int, WorkSchedule>
     */
    public function all(): Collection;

    public function findById(int $id): ?WorkSchedule;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): WorkSchedule;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, WorkSchedule>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
