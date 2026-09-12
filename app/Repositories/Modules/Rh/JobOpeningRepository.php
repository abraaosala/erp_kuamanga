<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Candidate;
use App\Models\JobOpening;
use App\Repositories\Contracts\JobOpeningRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobOpeningRepository implements JobOpeningRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    /**
     * @return Collection<int, JobOpening>
     */
    public function all(): Collection
    {
        /** @var Collection<int, JobOpening> $openings */
        $openings = JobOpening::where('empresa_id', $this->empresaId())
            ->with('department', 'position')
            ->orderByDesc('created_at')
            ->get();

        return $openings;
    }

    public function findById(int $id): ?JobOpening
    {
        /** @var \App\Models\JobOpening|null $opening */
        $opening = JobOpening::where('empresa_id', $this->empresaId())
            ->with('department', 'position')
            ->find($id);

        return $opening;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): JobOpening
    {
        $data['empresa_id'] ??= $this->empresaId();
        return JobOpening::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->findById($id);
        if (!$model) {
            return false;
        }
        return $model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->findById($id);
        if (!$model) {
            return false;
        }
        return (bool) $model->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = JobOpening::where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return $q->with('department', 'position')
            ->withCount('candidates')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function candidatesOf(int $id): Collection
    {
        /** @var Collection<int, Candidate> $candidates */
        $candidates = Candidate::where('empresa_id', $this->empresaId())
            ->where('job_opening_id', $id)
            ->with('interviews')
            ->orderByDesc('created_at')
            ->get();

        return $candidates;
    }

    /**
     * @return array{total: int, abertas: int, candidaturas: int, contratados: int}
     */
    public function summary(): array
    {
        return [
            'total'        => JobOpening::where('empresa_id', $this->empresaId())->count(),
            'abertas'      => JobOpening::where('empresa_id', $this->empresaId())->where('status', JobOpening::STATUS_ABERTA)->count(),
            'candidaturas' => Candidate::where('empresa_id', $this->empresaId())->count(),
            'contratados'  => Candidate::where('empresa_id', $this->empresaId())->where('status', Candidate::STATUS_CONTRATADO)->count(),
        ];
    }
}
