<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Benefit;
use App\Models\Employee;
use App\Repositories\Contracts\BenefitRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Services\Contracts\BenefitServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BenefitService implements BenefitServiceInterface
{
    protected BenefitPolicy $benefitPolicy;

    public function __construct(
        protected BenefitRepositoryInterface $benefitRepository,
        protected EmployeeRepositoryInterface $employeeRepository
    ) {
        $this->benefitPolicy = new BenefitPolicy();
    }

    /**
     * @return Collection<int, Benefit>
     */
    public function getAll(): Collection
    {
        return $this->benefitRepository->all();
    }

    public function getById(int $id): ?Benefit
    {
        return $this->benefitRepository->findById($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Benefit
    {
        /** @var mixed $rawTenure */
        $rawTenure = $data['min_tenure_months'] ?? 0;
        $data['min_tenure_months'] = max(0, is_numeric($rawTenure) ? (int) $rawTenure : 0);
        $data['status'] ??= Benefit::STATUS_ACTIVE;

        return $this->benefitRepository->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        if (array_key_exists('min_tenure_months', $data)) {
            /** @var mixed $rawTenure */
            $rawTenure = $data['min_tenure_months'];
            $data['min_tenure_months'] = max(0, is_numeric($rawTenure) ? (int) $rawTenure : 0);
        }

        return $this->benefitRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->benefitRepository->delete($id);
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->benefitRepository->paginate($perPage, $search);
    }

    /**
     * @return Collection<int, Employee>
     */
    public function employeesByBenefit(int $benefitId): Collection
    {
        return $this->benefitRepository->employeesByBenefit($benefitId);
    }

    /**
     * @return Collection<int, Employee>
     */
    public function eligibleEmployees(int $benefitId): Collection
    {
        $benefit = $this->requireBenefit($benefitId);

        return $this->benefitRepository->employeesNotAssigned($benefitId)
            ->filter(fn (Employee $employee): bool => $this->benefitPolicy->isEligible($employee, $benefit))
            ->values();
    }

    public function assign(int $benefitId, int $employeeId, ?string $startedAt = null, ?string $notes = null): bool
    {
        $benefit = $this->requireBenefit($benefitId);

        $employee = $this->employeeRepository->findById($employeeId);
        if (!$employee) {
            throw new \RuntimeException('Funcionário não encontrado.');
        }

        if ($this->benefitRepository->existsAssignment($benefitId, $employeeId)) {
            throw new \RuntimeException('O funcionário já possui este benefício atribuído.');
        }

        $violations = $this->benefitPolicy->violations($employee, $benefit);
        if ($violations !== []) {
            throw new \RuntimeException(
                'Funcionário não elegível para o benefício: ' . implode(' ', $violations)
            );
        }

        return $this->benefitRepository->assign($benefitId, $employeeId, [
            'status'     => Benefit::STATUS_ACTIVE,
            'started_at' => $startedAt,
            'notes'      => $notes,
        ]);
    }

    public function unassign(int $benefitId, int $employeeId): bool
    {
        $this->requireBenefit($benefitId);

        return $this->benefitRepository->unassign($benefitId, $employeeId);
    }

    public function isEligible(int $benefitId, int $employeeId): bool
    {
        $benefit = $this->requireBenefit($benefitId);

        $employee = $this->employeeRepository->findById($employeeId);
        if (!$employee) {
            return false;
        }

        return $this->benefitPolicy->isEligible($employee, $benefit);
    }

    /**
     * @return array{total: int, active: int, assignments: int}
     */
    public function summary(): array
    {
        /** @var Collection<int, Benefit> $benefits */
        $benefits = $this->benefitRepository->all();
        $counts = $this->benefitRepository->assignedCounts();

        $total = 0;
        $active = 0;
        foreach ($benefits as $benefit) {
            /** @var Benefit $benefit */
            $total++;
            if ($benefit->status === Benefit::STATUS_ACTIVE) {
                $active++;
            }
        }

        return [
            'total'       => $total,
            'active'      => $active,
            'assignments' => array_sum($counts),
        ];
    }

    /**
     * @return array<int, int>
     */
    public function assignedCounts(): array
    {
        return $this->benefitRepository->assignedCounts();
    }

    private function requireBenefit(int $benefitId): Benefit
    {
        $benefit = $this->benefitRepository->findById($benefitId);
        if (!$benefit) {
            throw new \RuntimeException('Benefício não encontrado.');
        }

        return $benefit;
    }
}