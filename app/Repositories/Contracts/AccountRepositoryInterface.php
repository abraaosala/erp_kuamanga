<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\AccountPlan;
use Illuminate\Support\Collection;

interface AccountRepositoryInterface
{
    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\AccountPlan>
     */
    public function allByEmpresa(int $empresaId): Collection;

    public function findById(int $id): ?AccountPlan;

    public function findByCode(int $empresaId, string $code): ?AccountPlan;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): AccountPlan;

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\AccountPlan>
     */
    public function getHierarchy(int $empresaId): Collection;

    /**
     * @template TReturn
     * @param \Closure(): TReturn $callback
     * @return TReturn
     */
    public function transaction(\Closure $callback): mixed;
}
