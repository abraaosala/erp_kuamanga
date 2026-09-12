<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Candidate;
use App\Repositories\Contracts\CandidateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CandidateRepository implements CandidateRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Candidate
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Candidate::create($data);
    }

    public function findById(int $id): ?Candidate
    {
        /** @var \App\Models\Candidate|null $candidate */
        $candidate = Candidate::where('empresa_id', $this->empresaId())
            ->with('jobOpening', 'employee')
            ->find($id);

        return $candidate;
    }

    public function findByEmail(string $email): ?Candidate
    {
        /** @var \App\Models\Candidate|null $candidate */
        $candidate = Candidate::where('empresa_id', $this->empresaId())
            ->where('email', $email)
            ->first();

        return $candidate;
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

    public function paginate(int $perPage = 15, ?string $search = null, ?string $status = null, ?int $jobOpeningId = null): LengthAwarePaginator
    {
        $q = Candidate::where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $q->where('status', $status);
        }

        if ($jobOpeningId) {
            $q->where('job_opening_id', $jobOpeningId);
        }

        return $q->with('jobOpening')
            ->withCount('interviews')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function byOpening(int $jobOpeningId): Collection
    {
        /** @var Collection<int, Candidate> $candidates */
        $candidates = Candidate::where('empresa_id', $this->empresaId())
            ->where('job_opening_id', $jobOpeningId)
            ->with('interviews')
            ->orderByDesc('created_at')
            ->get();

        return $candidates;
    }

    /**
         * @return array<string, int>
         */
    public function countByStatus(): array
    {
        /** @var array<string, int|string> $rows */
        $rows = Candidate::where('empresa_id', $this->empresaId())
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->all();

        $defaults = array_fill_keys(Candidate::STATUSES, 0);

        return array_map(static fn(int|string $count): int => (int) $count, array_merge($defaults, $rows));
    }

    public function countHired(): int
    {
        return Candidate::where('empresa_id', $this->empresaId())
            ->where('status', Candidate::STATUS_CONTRATADO)
            ->count();
    }
}
