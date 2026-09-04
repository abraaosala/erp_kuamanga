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
        /** @var \Illuminate\Support\Collection<int, \App\Models\AccountPlan> $result */
        $result = $this->model->where('empresa_id', $empresaId)->orderBy('code')->get();

        return $result;
    }

    public function findById(int $id): ?AccountPlan
    {
        /** @var \App\Models\AccountPlan|null $account */
        $account = $this->model->find($id);

        return $account;
    }

    public function findByCode(int $empresaId, string $code): ?AccountPlan
    {
        /** @var \App\Models\AccountPlan|null $account */
        $account = $this->model->where('empresa_id', $empresaId)->where('code', $code)->first();

        return $account;
    }

    public function create(array $data): AccountPlan
    {
        /** @var \App\Models\AccountPlan $account */
        $account = $this->model->create($data);

        return $account;
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
        $query = AccountPlan::query();
        /** @var \Illuminate\Database\Eloquent\Builder<AccountPlan> $query */
        $query->where('empresa_id', $empresaId)->whereNull('parent_id');

        /** @var \Illuminate\Support\Collection<int, \App\Models\AccountPlan> $result */
        $result = $query->with('children')->orderBy('code')->get();

        return $result;
    }

    public function transaction(\Closure $callback): mixed
    {
        return \Illuminate\Database\Capsule\Manager::connection()->transaction($callback);
    }
}
