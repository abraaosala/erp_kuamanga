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
        return current_empresa()->id;
    }

    public function all(): Collection
    {
        return Employee::with('position', 'department')
            ->where('empresa_id', $this->empresaId())
            ->get();
    }

    public function findById(int $id): ?Employee
    {
        return Employee::with('position', 'department')
            ->where('empresa_id', $this->empresaId())
            ->find($id);
    }

    public function create(array $data): Employee
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Employee::create($data);
    }

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
