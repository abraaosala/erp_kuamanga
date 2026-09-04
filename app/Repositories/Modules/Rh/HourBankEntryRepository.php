<?php

declare(strict_types=1);

namespace App\Repositories\Modules\Rh;

use App\Models\HourBankEntry;
use App\Repositories\Contracts\HourBankEntryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HourBankEntryRepository implements HourBankEntryRepositoryInterface
{
    protected function empresaId(): int
    {
        return current_empresa()->id;
    }

    public function all(): Collection
    {
        return HourBankEntry::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->get();
    }

    public function findById(int $id): ?HourBankEntry
    {
        return HourBankEntry::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->find($id);
    }

    public function create(array $data): HourBankEntry
    {
        $data['empresa_id'] ??= $this->empresaId();
        return HourBankEntry::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $entry = $this->findById($id);
        if (!$entry) {
            return false;
        }
        return $entry->update($data);
    }

    public function delete(int $id): bool
    {
        $entry = $this->findById($id);
        if (!$entry) {
            return false;
        }
        return (bool) $entry->delete();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $q = HourBankEntry::with('employee')
            ->where('empresa_id', $this->empresaId());

        if ($search) {
            $q->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        return $q->orderBy('date', 'desc')->paginate($perPage);
    }

    public function balanceByEmployee(int $employeeId): float
    {
        return (float) HourBankEntry::where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->sum('hours');
    }

    public function summary(): array
    {
        $entries = HourBankEntry::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->get();

        $totals = [];
        foreach ($entries as $entry) {
            $employee = $entry->employee;
            if (!$employee) {
                continue;
            }
            $name = $employee->name;
            $totals[$name] = ($totals[$name] ?? 0) + (float) $entry->hours;
        }

        arsort($totals);

        $result = [];
        foreach ($totals as $name => $total) {
            $result[] = ['employee' => $name, 'balance' => $total];
        }

        return $result;
    }
}
