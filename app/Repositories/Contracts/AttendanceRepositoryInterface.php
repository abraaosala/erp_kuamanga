<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Attendance;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Attendance;

    public function create(array $data): Attendance;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;
}
