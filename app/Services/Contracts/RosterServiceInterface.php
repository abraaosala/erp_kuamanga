<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Rotation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RosterServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rotation>
     */
    public function getAllRotations(): Collection;

    public function getRotationById(int $id): ?Rotation;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Rotation>
     */
    public function paginateRotations(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function generate(array $data): Rotation;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function events(string $start, string $end, ?int $rotationId = null): array;

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

    public function deleteRotation(int $id): bool;
}