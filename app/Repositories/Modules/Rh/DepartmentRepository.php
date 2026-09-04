<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Department;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var Collection<int, Department> $departments */
        $departments = Department::where('empresa_id', $this->empresaId())->orderBy('name')->get();

        return $departments;
    }

    public function findById(int $id): ?Department
    {
        /** @var \App\Models\Department|null $department */
        $department = Department::where('empresa_id', $this->empresaId())->find($id);

        return $department;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Department
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Department::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->findById($id);
        if (!$model) {
            return false;
        }
        return $model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->findById($id);
        if (!$model) {
            return false;
        }
        return (bool) $model->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = Department::where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $q->orderBy('name')->paginate($perPage);
    }
}
