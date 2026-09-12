<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Interview;
use Illuminate\Database\Eloquent\Collection;

interface InterviewServiceInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Interview;

    public function updateResult(int $id, string $result): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Interview>
     */
    public function byCandidate(int $candidateId): Collection;
}
