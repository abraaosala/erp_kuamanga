<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Attendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceRepositoryInterface
{
    /**
     * @return Collection<int, Attendance>
     */
    public function all(): Collection;

    public function findById(int $id): ?Attendance;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Attendance;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, Attendance>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
