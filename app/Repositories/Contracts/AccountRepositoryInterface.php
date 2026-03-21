<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\AccountPlan;
use Illuminate\Support\Collection;

interface AccountRepositoryInterface
{
    public function allByEmpresa(int $empresaId): Collection;
    
    public function findById(int $id): ?AccountPlan;
    
    public function findByCode(int $empresaId, string $code): ?AccountPlan;
    
    public function create(array $data): AccountPlan;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getHierarchy(int $empresaId): Collection;
    
    public function transaction(\Closure $callback): mixed;
}
