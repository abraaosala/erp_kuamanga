<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\EmployeeDocument;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeDocumentRepositoryInterface
{
    /**
     * @return Collection<int, EmployeeDocument>
     */
    public function findByEmployee(int $employeeId): Collection;

    /**
     * @return Collection<int, EmployeeDocument>
     */
    public function findByType(int $employeeId, string $type): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): EmployeeDocument;

    public function findById(int $id): ?EmployeeDocument;

    public function delete(int $id): bool;
}
