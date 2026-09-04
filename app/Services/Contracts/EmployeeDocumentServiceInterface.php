<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\EmployeeDocument;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeDocumentServiceInterface
{
    /**
     * @return Collection<int, EmployeeDocument>
     */
    public function getByEmployee(int $employeeId): Collection;

    /**
     * @return Collection<int, EmployeeDocument>
     */
    public function getByType(int $employeeId, string $type): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): EmployeeDocument;

    public function getById(int $id): ?EmployeeDocument;

    public function delete(int $id): bool;
}
