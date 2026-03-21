<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Accounting;

use App\Models\AccountPlan;
use App\Repositories\Contracts\AccountRepositoryInterface;
use Illuminate\Support\Collection;

class AccountRepository implements AccountRepositoryInterface
{
    protected AccountPlan $model;

    public function __construct(AccountPlan $model)
    {
        $this->model = $model;
    }

    public function allByEmpresa(int $empresaId): Collection
    {
        return $this->model->where('empresa_id', $empresaId)->orderBy('code')->get();
    }

    public function findById(int $id): ?AccountPlan
    {
        return $this->model->find($id);
    }

    public function findByCode(int $empresaId, string $code): ?AccountPlan
    {
        return $this->model->where('empresa_id', $empresaId)->where('code', $code)->first();
    }

    public function create(array $data): AccountPlan
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $account = $this->findById($id);
        if (!$account) {
            return false;
        }
        return $account->update($data);
    }

    public function delete(int $id): bool
    {
        $account = $this->findById($id);
        if (!$account) {
            return false;
        }
        return (bool)$account->delete();
    }

    public function getHierarchy(int $empresaId): Collection
    {
        return $this->model->where('empresa_id', $empresaId)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('code')
            ->get();
    }

    public function transaction(\Closure $callback): mixed
    {
        return \Illuminate\Database\Capsule\Manager::connection()->transaction($callback);
    }
}
