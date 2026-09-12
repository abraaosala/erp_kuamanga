<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\JobOpening;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobOpeningRepositoryInterface
{
    /**
     * @return Collection<int, JobOpening>
     */
    public function all(): Collection;

    public function findById(int $id): ?JobOpening;

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
     * @return LengthAwarePaginator<int, JobOpening>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidate>
     */
    public function candidatesOf(int $id): Collection;

    /**
     * @return array{total: int, abertas: int, candidaturas: int, contratados: int}
     */
    public function summary(): array;
}
