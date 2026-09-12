<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Interview;
use Illuminate\Database\Eloquent\Collection;

interface InterviewRepositoryInterface
{
    public function findById(int $id): ?Interview;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Interview;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return Collection<int, Interview>
     */
    public function findByCandidate(int $candidateId): Collection;
}
