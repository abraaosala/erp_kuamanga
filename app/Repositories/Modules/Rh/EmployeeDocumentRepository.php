<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\EmployeeDocument;
use App\Repositories\Contracts\EmployeeDocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EmployeeDocumentRepository implements EmployeeDocumentRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function findByEmployee(int $employeeId): Collection
    {
        /** @var Collection<int, EmployeeDocument> $result */
        $result = EmployeeDocument::where('employee_id', $employeeId)
            ->where('empresa_id', $this->empresaId())
            ->orderBy('document_type')
            ->orderBy('created_at', 'desc')
            ->get();

        return $result;
    }

    public function findByType(int $employeeId, string $type): Collection
    {
        /** @var Collection<int, EmployeeDocument> $result */
        $result = EmployeeDocument::where('employee_id', $employeeId)
            ->where('empresa_id', $this->empresaId())
            ->where('document_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): EmployeeDocument
    {
        $data['empresa_id'] ??= $this->empresaId();
        return EmployeeDocument::create($data);
    }

    public function findById(int $id): ?EmployeeDocument
    {
        /** @var EmployeeDocument|null $doc */
        $doc = EmployeeDocument::where('empresa_id', $this->empresaId())
            ->find($id);

        return $doc;
    }

    public function delete(int $id): bool
    {
        $doc = $this->findById($id);
        if (!$doc) {
            return false;
        }
        return (bool) $doc->delete();
    }
}
