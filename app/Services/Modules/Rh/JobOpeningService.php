<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\JobOpening;
use App\Repositories\Contracts\JobOpeningRepositoryInterface;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Services\Contracts\JobOpeningServiceInterface;
use App\Support\DepartmentPositionRule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobOpeningService implements JobOpeningServiceInterface
{
    public function __construct(
        protected JobOpeningRepositoryInterface $jobOpeningRepository,
        protected PositionRepositoryInterface $positionRepository,
    ) {}

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, JobOpening>
     */
    public function getAll(): Collection
    {
        return $this->jobOpeningRepository->all();
    }

    public function getById(int $id): ?JobOpening
    {
        return $this->jobOpeningRepository->findById($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): JobOpening
    {
        if (empty($data['title'])) {
            throw new \InvalidArgumentException('O título da vaga é obrigatório.');
        }

        $openingsCount = $data['openings_count'] ?? 1;
        $data['openings_count'] = max(1, is_numeric($openingsCount) ? (int) $openingsCount : 1);
        $data['status'] ??= JobOpening::STATUS_ABERTA;

        if (!in_array($data['status'], JobOpening::STATUSES, true)) {
            throw new \InvalidArgumentException('Estado da vaga inválido.');
        }

        $data['department_id'] = $this->resolveDepartment($data);

        return $this->jobOpeningRepository->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        if (array_key_exists('openings_count', $data)) {
            $data['openings_count'] = max(1, is_numeric($data['openings_count']) ? (int) $data['openings_count'] : 1);
        }

        if (array_key_exists('status', $data) && !in_array($data['status'], JobOpening::STATUSES, true)) {
            throw new \InvalidArgumentException('Estado da vaga inválido.');
        }

        $data['department_id'] = $this->resolveDepartment($data, $id);

        return $this->jobOpeningRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->jobOpeningRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->jobOpeningRepository->paginate($perPage, $search);
    }

    public function changeStatus(int $id, string $status): bool
    {
        if (!in_array($status, JobOpening::STATUSES, true)) {
            throw new \InvalidArgumentException('Estado da vaga inválido.');
        }

        $opening = $this->jobOpeningRepository->findById($id);
        if (!$opening) {
            return false;
        }

        return $this->jobOpeningRepository->update($id, ['status' => $status]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Candidate>
     */
    public function byOpening(int $id): Collection
    {
        return $this->jobOpeningRepository->candidatesOf($id);
    }

    /**
     * @return array{total: int, abertas: int, candidaturas: int, contratados: int}
     */
    public function summary(): array
    {
        return $this->jobOpeningRepository->summary();
    }

    /**
     * Deriva o departamento da vaga a partir do cargo escolhido.
     *
     * @param array<string, mixed> $data
     * @param int|null             $existingId vaga existente (para update)
     */
    private function resolveDepartment(array $data, ?int $existingId = null): ?int
    {
        $departmentId = array_key_exists('department_id', $data)
            ? $this->nullableId($data['department_id'])
            : null;

        $positionId = null;
        if (array_key_exists('position_id', $data)) {
            $positionId = $this->nullableId($data['position_id']);
        } elseif ($existingId !== null) {
            $positionId = $this->jobOpeningRepository->findById($existingId)?->position_id;
        }

        if ($positionId === null) {
            return $departmentId;
        }

        $position = $this->positionRepository->findById($positionId);
        if (!$position) {
            throw new \InvalidArgumentException('Cargo não encontrado.');
        }

        return DepartmentPositionRule::resolve(
            $departmentId,
            $position->department_id !== null ? (int) $position->department_id : null,
        );
    }

    private function nullableId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}
