<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Candidate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CandidateRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Candidate;

    public function findById(int $id): ?Candidate;

    public function findByEmail(string $email): ?Candidate;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, Candidate>
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?string $status = null, ?int $jobOpeningId = null): LengthAwarePaginator;

    /**
     * @return Collection<int, Candidate>
     */
    public function byOpening(int $jobOpeningId): Collection;

    /**
     * Contagem de candidatos por estado da pipeline.
     *
     * @return array<string, int>
     */
    public function countByStatus(): array;

    public function countHired(): int;
}
