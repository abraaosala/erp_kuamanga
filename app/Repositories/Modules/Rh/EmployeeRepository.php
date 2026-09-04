<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var Collection<int, Employee> $result */
        $result = Employee::with('position', 'department')
            ->where('empresa_id', $this->empresaId())
            ->get();

        return $result;
    }

    public function findById(int $id): ?Employee
    {
        /** @var \App\Models\Employee|null $employee */
        $employee = Employee::with('position', 'department')
            ->where('empresa_id', $this->empresaId())
            ->find($id);

        return $employee;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Employee
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Employee::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $employee = $this->findById($id);
        if (!$employee) {
            return false;
        }
        return $employee->update($data);
    }

    public function delete(int $id): bool
    {
        $employee = $this->findById($id);
        if (!$employee) {
            return false;
        }
        return (bool) $employee->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = Employee::with('position', 'department')
            ->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $q->orderBy('name')->paginate($perPage);
    }
}
