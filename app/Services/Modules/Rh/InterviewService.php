<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Candidate;
use App\Models\Interview;
use App\Repositories\Contracts\CandidateRepositoryInterface;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use App\Services\Contracts\InterviewServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class InterviewService implements InterviewServiceInterface
{
    public function __construct(
        protected InterviewRepositoryInterface $interviewRepository,
        protected CandidateRepositoryInterface $candidateRepository,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Interview
    {
        if (empty($data['candidate_id'])) {
            throw new \InvalidArgumentException('O candidato da entrevista é obrigatório.');
        }

        $candidateId = is_scalar($data['candidate_id']) ? (int) $data['candidate_id'] : 0;
        $candidate = $this->candidateRepository->findById($candidateId);
        if (!$candidate) {
            throw new \InvalidArgumentException('Candidato não encontrado.');
        }
        if (in_array($candidate->status, Candidate::TERMINAL_STATUSES, true)) {
            throw new \InvalidArgumentException('Não é possível agendar entrevistas para candidatos em estado terminal.');
        }

        $data['job_opening_id'] ??= $candidate->job_opening_id;
        $data['result'] ??= Interview::RESULT_PENDENTE;

        $this->assertValidResult($data['result']);

        return $this->interviewRepository->create($data);
    }

    public function updateResult(int $id, string $result): bool
    {
        $this->assertValidResult($result);

        return $this->interviewRepository->update($id, ['result' => $result]);
    }

    public function delete(int $id): bool
    {
        return $this->interviewRepository->delete($id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Interview>
     */
    public function byCandidate(int $candidateId): Collection
    {
        return $this->interviewRepository->findByCandidate($candidateId);
    }

    private function assertValidResult(mixed $result): void
    {
        if (!is_string($result) || !in_array($result, Interview::RESULTS, true)) {
            throw new \InvalidArgumentException('Resultado da entrevista inválido.');
        }
    }
}
