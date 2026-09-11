<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Rotation;
use App\Models\ScheduledShift;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RosterRepositoryInterface
{
    /**
     * @return Collection<int, Rotation>
     */
    public function allRotations(): Collection;

    public function findRotationById(int $id): ?Rotation;

    /**
     * @param array<string, mixed> $data
     */
    public function createRotation(array $data): Rotation;

    /**
     * @param array<string, mixed> $data
     */
    public function updateRotation(int $id, array $data): bool;

    public function deleteRotation(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, Rotation>
     */
    public function paginateRotations(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function insertShifts(array $rows): void;

    public function deleteShiftsByRotation(int $rotationId): int;

    /**
     * @param list<int> $employeeIds
     */
    public function deleteGeneratedBetween(string $start, string $end, array $employeeIds = []): int;

    public function deleteShift(int $shiftId): bool;

    /**
     * @return array{
     *     today_work: int,
     *     today_off: int,
     *     total_shifts: int,
     *     rotations_count: int,
     *     employees_covered: int
     * }
     */
    public function stats(string $today): array;

    /**
     * @return Collection<int, ScheduledShift>
     */
    public function shiftsBetween(string $start, string $end, ?int $rotationId = null): Collection;
}
