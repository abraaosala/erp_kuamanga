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
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var Collection<int, Contract> $contracts */
        $contracts = Contract::with('employee.position')
            ->where('empresa_id', $this->empresaId())
            ->orderBy('data_inicio', 'desc')
            ->get();

        return $contracts;
    }

    public function findById(int $id): ?Contract
    {
        /** @var \App\Models\Contract|null $contract */
        $contract = Contract::with('employee.position')
            ->where('empresa_id', $this->empresaId())
            ->find($id);

        return $contract;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Contract
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Contract::create($data);
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
        /** @var Collection<int, Contract> $contracts */
        $contracts = Contract::with('employee.position')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->orderBy('data_inicio', 'desc')
            ->get();

        return $contracts;
    }
}
