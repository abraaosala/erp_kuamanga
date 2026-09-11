<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\Benefit;
use App\Models\Employee;
use App\Repositories\Contracts\BenefitRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BenefitRepository implements BenefitRepositoryInterface
{
    protected function empresaId(): int
    {
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    /**
     * @return Collection<int, Benefit>
     */
    public function all(): Collection
    {
        /** @var Collection<int, Benefit> $result */
        $result = Benefit::with(['position', 'department'])
            ->where('empresa_id', $this->empresaId())
            ->orderBy('name')
            ->get();

        return $result;
    }

    public function findById(int $id): ?Benefit
    {
        /** @var \App\Models\Benefit|null $benefit */
        $benefit = Benefit::with(['position', 'department'])
            ->where('empresa_id', $this->empresaId())
            ->find($id);

        return $benefit;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Benefit
    {
        $data['empresa_id'] ??= $this->empresaId();
        return Benefit::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $benefit = $this->findById($id);
        if (!$benefit) {
            return false;
        }
        return $benefit->update($data);
    }

    public function delete(int $id): bool
    {
        $benefit = $this->findById($id);
        if (!$benefit) {
            return false;
        }
        return (bool) $benefit->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = Benefit::with(['position', 'department'])
            ->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->where('name', 'like', "%{$search}%");
        }

        return $q->orderBy('name')->paginate($perPage);
    }

    /**
     * @return Collection<int, Employee>
     */
    public function employeesByBenefit(int $benefitId): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Employee> $employees */
        $employees = Employee::with(['position', 'department'])
            ->where('empresa_id', $this->empresaId())
            ->whereHas('benefits', function ($q) use ($benefitId) {
                $q->where('employee_benefits.benefit_id', $benefitId);
            })
            ->orderBy('name')
            ->get();

        return $employees;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function employeesNotAssigned(int $benefitId): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Employee> $employees */
        $employees = Employee::with(['position', 'department'])
            ->where('empresa_id', $this->empresaId())
            ->whereDoesntHave('benefits', function ($q) use ($benefitId) {
                $q->where('employee_benefits.benefit_id', $benefitId);
            })
            ->orderBy('name')
            ->get();

        return $employees;
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function assign(int $benefitId, int $employeeId, array $meta = []): bool
    {
        if ($this->existsAssignment($benefitId, $employeeId)) {
            return false;
        }

        DB::table('employee_benefits')->insert(array_merge([
            'empresa_id'  => $this->empresaId(),
            'employee_id' => $employeeId,
            'benefit_id'  => $benefitId,
            'status'      => $meta['status'] ?? Benefit::STATUS_ACTIVE,
            'started_at'  => $meta['started_at'] ?? null,
            'notes'       => $meta['notes'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]));

        return true;
    }

    public function unassign(int $benefitId, int $employeeId): bool
    {
        return (bool) DB::table('employee_benefits')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->where('benefit_id', $benefitId)
            ->delete();
    }

    public function existsAssignment(int $benefitId, int $employeeId): bool
    {
        return DB::table('employee_benefits')
            ->where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->where('benefit_id', $benefitId)
            ->exists();
    }

    /**
     * @return array<int, int>
     */
    public function assignedCounts(): array
    {
        $rows = DB::table('employee_benefits')
            ->select('benefit_id')
            ->selectRaw('COUNT(*) as total')
            ->where('empresa_id', $this->empresaId())
            ->groupBy('benefit_id')
            ->get();

        $counts = [];
        foreach ($rows as $row) {
            /** @var \stdClass $row */
            $counts[(int) $row->benefit_id] = (int) $row->total;
        }

        return $counts;
    }
}
