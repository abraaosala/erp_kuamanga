<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Candidate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CandidateServiceInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Candidate;

    public function getById(int $id): ?Candidate;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Candidate>
     */
    public function paginate(int $perPage = 15, ?string $search = null, ?string $status = null, ?int $jobOpeningId = null): LengthAwarePaginator;

    /**
     * @return Collection<int, Candidate>
     */
    public function byOpening(int $jobOpeningId): Collection;

    /**
     * Avança o candidato para a fase explicita da pipeline (triagem → entrevista → aprovado).
     */
    public function transition(int $id, string $stage, ?int $decidedBy = null): Candidate;

    public function reject(int $id, ?int $decidedBy = null): Candidate;

    /**
     * Contrata o candidato aprovado: cria o registo de funcionário e fecha a pipeline.
     */
    public function hire(int $id, ?int $decidedBy = null): Candidate;

    /**
     * @return array<string, int>
     */
    public function countByStatus(): array;

    /**
     * @return array{total: int, novos: int, triagem: int, entrevista: int, aprovados: int, contratados: int, rejeitados: int}
     */
    public function summary(): array;
}
