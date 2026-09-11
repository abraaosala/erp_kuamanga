<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\LeaveRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LeaveServiceInterface
{
    /**
     * @return Collection<int, LeaveRequest>
     */
    public function getAll(): Collection;

    public function getById(int $id): ?LeaveRequest;

    /**
     * Cria um pedido de férias/licença (status pendente), validando
     * dias, saldo de férias e sobreposição com férias já aprovadas.
     *
     * @param array<string, mixed> $data
     */
    public function requestLeave(array $data): LeaveRequest;

    /**
     * Aprova um pedido pendente e regista a férias/licença efectiva.
     */
    public function approve(int $id, ?int $decidedBy = null, ?string $notes = null): LeaveRequest;

    /**
     * Rejeita um pedido pendente, registando a decisão.
     */
    public function reject(int $id, ?int $decidedBy = null, ?string $notes = null): LeaveRequest;

    /**
     * Cancela um pedido pendente (não gera registo de férias efectivas).
     */
    public function cancel(int $id): LeaveRequest;

    public function delete(int $id): bool;

    /**
     * @return LengthAwarePaginator<int, LeaveRequest>
     */
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function countPending(): int;

    /**
     * @return array{entitled: int, used: int, available: int}
     */
    public function balanceByEmployee(int $employeeId): array;

    /**
     * Resumo do saldo de férias de todos os funcionários.
     *
     * @return array<int, array{employee: string, entitled: int, used: int, available: int}>
     */
    public function balances(): array;
}
