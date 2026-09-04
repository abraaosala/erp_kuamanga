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
        /** @var \App\Models\Empresa $empresa */
        $empresa = current_empresa();
        return $empresa->id;
    }

    public function all(): Collection
    {
        /** @var Collection<int, HourBankEntry> $result */
        $result = HourBankEntry::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->get();

        return $result;
    }

    public function findById(int $id): ?HourBankEntry
    {
        /** @var \App\Models\HourBankEntry|null $entry */
        $entry = HourBankEntry::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->find($id);

        return $entry;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): HourBankEntry
    {
        $data['empresa_id'] ??= $this->empresaId();
        return HourBankEntry::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
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
        /** @var mixed $sum */
        $sum = HourBankEntry::where('empresa_id', $this->empresaId())
            ->where('employee_id', $employeeId)
            ->sum('hours');

        return is_numeric($sum) ? (float) $sum : 0.0;
    }

    /**
     * @return array<int, array{employee: string, balance: float}>
     */
    public function summary(): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\HourBankEntry> $entries */
        $entries = HourBankEntry::with('employee')
            ->where('empresa_id', $this->empresaId())
            ->get();

        $totals = [];
        foreach ($entries as $entry) {
            /** @var \App\Models\HourBankEntry $entry */
            /** @var \App\Models\Employee|null $employee */
            $employee = $entry->employee;
            if (!$employee) {
                continue;
            }
            $name = $employee->name;
            /** @var mixed $hours */
            $hours = $entry->hours;
            $totals[$name] = ($totals[$name] ?? 0) + (is_numeric($hours) ? (float) $hours : 0.0);
        }

        arsort($totals);

        $result = [];
        foreach ($totals as $name => $total) {
            $result[] = ['employee' => $name, 'balance' => $total];
        }

        return $result;
    }
}
