<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Position;
use App\Repositories\Contracts\PositionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PositionRepository implements PositionRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var Collection<int, Position> $positions */
        $positions = Position::with('department')->where('empresa_id', $this->empresaId())->orderBy('name')->get();

        return $positions;
    }

    public function findById(int $id): ?Position
    {
        /** @var \App\Models\Position|null $position */
        $position = Position::where('empresa_id', $this->empresaId())->find($id);

        return $position;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Position
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Position::create($data);
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
        $q = Position::with('department')->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $q->orderBy('name')->paginate($perPage);
    }

    public function findByDepartment(int $departmentId): Collection
    {
        /** @var Collection<int, Position> $positions */
        $positions = Position::with('department')
            ->where('empresa_id', $this->empresaId())
            ->where('department_id', $departmentId)
            ->orderBy('name')->get();

        return $positions;
    }
}
