<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Interview;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class InterviewRepository implements InterviewRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function findById(int $id): ?Interview
    {
        /** @var \App\Models\Interview|null $interview */
        $interview = Interview::where('empresa_id', $this->empresaId())->find($id);

        return $interview;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Interview
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Interview::create($data);
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

    /**
     * @return Collection<int, Interview>
     */
    public function findByCandidate(int $candidateId): Collection
    {
        /** @var Collection<int, Interview> $interviews */
        $interviews = Interview::where('empresa_id', $this->empresaId())
            ->where('candidate_id', $candidateId)
            ->orderByDesc('scheduled_at')
            ->get();

        return $interviews;
    }
}
