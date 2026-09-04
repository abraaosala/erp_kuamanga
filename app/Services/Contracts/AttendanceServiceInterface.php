<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Attendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?Attendance;

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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Attendance>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
