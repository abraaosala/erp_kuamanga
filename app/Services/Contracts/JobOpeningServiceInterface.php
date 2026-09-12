<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\JobOpening;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobOpeningServiceInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\JobOpening>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?JobOpening;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): JobOpening;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\JobOpening>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function changeStatus(int $id, string $status): bool;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidate>
     */
    public function byOpening(int $id): Collection;

    /**
     * @return array{total: int, abertas: int, candidaturas: int, contratados: int}
     */
    public function summary(): array;
}
