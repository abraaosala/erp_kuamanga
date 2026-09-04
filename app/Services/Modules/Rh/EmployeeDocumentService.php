<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\EmployeeDocument;
use App\Repositories\Contracts\EmployeeDocumentRepositoryInterface;
use App\Services\Contracts\EmployeeDocumentServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class EmployeeDocumentService implements EmployeeDocumentServiceInterface
{
    public function __construct(
        protected EmployeeDocumentRepositoryInterface $employeeDocumentRepository
    ) {}

    public function getByEmployee(int $employeeId): Collection
    {
        return $this->employeeDocumentRepository->findByEmployee($employeeId);
    }

    public function getByType(int $employeeId, string $type): Collection
    {
        return $this->employeeDocumentRepository->findByType($employeeId, $type);
    }

    public function create(array $data): EmployeeDocument
    {
        return $this->employeeDocumentRepository->create($data);
    }

    public function getById(int $id): ?EmployeeDocument
    {
        return $this->employeeDocumentRepository->findById($id);
    }

    public function delete(int $id): bool
    {
        return $this->employeeDocumentRepository->delete($id);
    }
}
