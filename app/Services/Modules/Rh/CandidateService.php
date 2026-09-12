<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Candidate;
use App\Models\JobOpening;
use App\Repositories\Contracts\CandidateRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\JobOpeningRepositoryInterface;
use App\Services\Contracts\CandidateServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CandidateService implements CandidateServiceInterface
{
    /** Fases permitidas a partir de cada estado da pipeline. */
    private const TRANSITIONS = [
        Candidate::STATUS_NOVO       => [Candidate::STATUS_TRIAGEM],
        Candidate::STATUS_TRIAGEM    => [Candidate::STATUS_ENTREVISTA],
        Candidate::STATUS_ENTREVISTA => [Candidate::STATUS_APROVADO],
        Candidate::STATUS_APROVADO   => [],
    ];

    public function __construct(
        protected CandidateRepositoryInterface $candidateRepository,
        protected JobOpeningRepositoryInterface $jobOpeningRepository,
        protected EmployeeRepositoryInterface $employeeRepository,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Candidate
    {
        if (empty($data['name']) || empty($data['email'])) {
            throw new \InvalidArgumentException('Nome e email do candidato são obrigatórios.');
        }
        if (!is_string($data['email'])) {
            throw new \InvalidArgumentException('Email do candidato inválido.');
        }

        $data['email'] = strtolower(trim($data['email']));

        if ($this->candidateRepository->findByEmail($data['email'])) {
            throw new \InvalidArgumentException('Já existe um candidato com este email.');
        }

        $this->assertOpeningAcceptingApplications($data['job_opening_id'] ?? null);
        $this->assertValidSource($data['source'] ?? null);

        $data['status'] ??= Candidate::STATUS_NOVO;
        $data['source'] ??= Candidate::SOURCE_SITE;
        if (in_array($data['status'], Candidate::TERMINAL_STATUSES, true)) {
            throw new \InvalidArgumentException('Não é possível criar um candidato num estado terminal.');
        }

        return $this->candidateRepository->create($data);
    }

    public function getById(int $id): ?Candidate
    {
        return $this->candidateRepository->findById($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $candidate = $this->requireCandidate($id);

        if (array_key_exists('email', $data) && !empty($data['email'])) {
            if (!is_string($data['email'])) {
                throw new \InvalidArgumentException('Email do candidato inválido.');
            }

            $data['email'] = strtolower(trim($data['email']));
            $existing = $this->candidateRepository->findByEmail($data['email']);
            if ($existing && (int) $existing->id !== $id) {
                throw new \InvalidArgumentException('Já existe um candidato com este email.');
            }
        }

        $this->assertOpeningAcceptingApplications($data['job_opening_id'] ?? $candidate->job_opening_id);

        if (array_key_exists('status', $data) && in_array($data['status'], Candidate::TERMINAL_STATUSES, true)) {
            throw new \InvalidArgumentException('O estado do candidato deve ser alterado através da pipeline.');
        }

        return $this->candidateRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->candidateRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null, ?string $status = null, ?int $jobOpeningId = null): LengthAwarePaginator
    {
        return $this->candidateRepository->paginate($perPage, $search, $status, $jobOpeningId);
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function byOpening(int $jobOpeningId): Collection
    {
        return $this->candidateRepository->byOpening($jobOpeningId);
    }

    public function transition(int $id, string $stage, ?int $decidedBy = null): Candidate
    {
        $candidate = $this->requireCandidate($id);

        if (!in_array($stage, self::TRANSITIONS[$candidate->status] ?? [], true)) {
            throw new \InvalidArgumentException(
                sprintf('Não é possível avançar de "%s" para "%s".', $candidate->status, $stage),
            );
        }

        return $this->markStatus($candidate, $stage, $decidedBy);
    }

    public function reject(int $id, ?int $decidedBy = null): Candidate
    {
        $candidate = $this->requireCandidate($id);

        if (in_array($candidate->status, Candidate::TERMINAL_STATUSES, true)) {
            throw new \InvalidArgumentException('O candidato já está num estado terminal.');
        }

        return $this->markStatus($candidate, Candidate::STATUS_REJEITADO, $decidedBy);
    }

    public function hire(int $id, ?int $decidedBy = null): Candidate
    {
        $candidate = $this->requireCandidate($id);

        if ($candidate->status !== Candidate::STATUS_APROVADO) {
            throw new \InvalidArgumentException('Apenas candidatos aprovados podem ser contratados.');
        }

        $employee = $this->employeeRepository->create([
            'name'          => (string) $candidate->name,
            'email'         => $candidate->email,
            'phone'         => $candidate->phone,
            'hire_date'     => date('Y-m-d'),
            'status'        => 'active',
            'department_id' => $candidate->job_opening_id ? $this->jobOpeningRepository->findById((int) $candidate->job_opening_id)?->department_id : null,
            'position_id'   => $candidate->job_opening_id ? $this->jobOpeningRepository->findById((int) $candidate->job_opening_id)?->position_id : null,
        ]);

        return $this->markStatus($candidate, Candidate::STATUS_CONTRATADO, $decidedBy, (int) $employee->id);
    }

    /**
     * @return array<string, int>
     */
    public function countByStatus(): array
    {
        return $this->candidateRepository->countByStatus();
    }

    /**
     * @return array{total: int, novos: int, triagem: int, entrevista: int, aprovados: int, contratados: int, rejeitados: int}
     */
    public function summary(): array
    {
        $counts = $this->candidateRepository->countByStatus();

        return [
            'total'       => array_sum($counts),
            'novos'       => $counts[Candidate::STATUS_NOVO] ?? 0,
            'triagem'     => $counts[Candidate::STATUS_TRIAGEM] ?? 0,
            'entrevista'  => $counts[Candidate::STATUS_ENTREVISTA] ?? 0,
            'aprovados'   => $counts[Candidate::STATUS_APROVADO] ?? 0,
            'contratados' => $counts[Candidate::STATUS_CONTRATADO] ?? 0,
            'rejeitados'  => $counts[Candidate::STATUS_REJEITADO] ?? 0,
        ];
    }

    private function requireCandidate(int $id): Candidate
    {
        $candidate = $this->candidateRepository->findById($id);
        if (!$candidate) {
            throw new \RuntimeException('Candidato não encontrado.');
        }

        return $candidate;
    }

    private function markStatus(Candidate $candidate, string $status, ?int $decidedBy, ?int $employeeId = null): Candidate
    {
        $this->candidateRepository->update((int) $candidate->id, array_filter([
            'status'      => $status,
            'decided_by'  => $decidedBy,
            'decided_at'  => date('Y-m-d H:i:s'),
            'employee_id' => $employeeId,
        ], static fn(mixed $value): bool => $value !== null));

        /** @var Candidate|null $updated */
        $updated = $this->candidateRepository->findById((int) $candidate->id);

        return $updated ?? $candidate;
    }

    private function assertOpeningAcceptingApplications(mixed $jobOpeningId): void
    {
        $openingId = is_scalar($jobOpeningId) ? (int) $jobOpeningId : null;
        if (!$openingId) {
            return;
        }

        $opening = $this->jobOpeningRepository->findById($openingId);
        if (!$opening) {
            throw new \InvalidArgumentException('Vaga não encontrada.');
        }
        if ($opening->status !== JobOpening::STATUS_ABERTA) {
            throw new \InvalidArgumentException('A vaga não está aberta a candidaturas.');
        }
    }

    private function assertValidSource(mixed $source): void
    {
        if ($source === null || $source === '') {
            return;
        }
        if (!is_string($source) || !in_array($source, Candidate::SOURCES, true)) {
            throw new \InvalidArgumentException('Origem do candidato inválida.');
        }
    }
}
