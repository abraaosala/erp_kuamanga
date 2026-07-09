<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Contract;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ContractRepository implements ContractRepositoryInterface
{
    protected function empresaId(): int
    {
        return current_empresa()->id;
    }

    public function all(): Collection
    {
        return Contract::with('employee.position')
            ->where('empresa_id', $this->empresaId())
            ->orderBy('data_inicio', 'desc')
            ->get();
    }

    public function findById(int $id): ?Contract
    {
        return Contract::with('employee.position')
            ->where('empresa_id', $this->empresaId())
            ->find($id);
    }

    public function create(array $data): Contract
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Contract::create($data);
    }

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
        $q = Contract::with('employee.position')
            ->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        return $q->orderBy('data_inicio', 'desc')->paginate($perPage);
    }

    public function findByEmployee(int $employeeId): Collection
    {
        return Contract::with('employee.position')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->orderBy('data_inicio', 'desc')
            ->get();
    }
}
