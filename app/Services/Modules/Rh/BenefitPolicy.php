<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

use App\Models\Benefit;
use App\Models\Employee;

/**
 * Regras de elegibilidade de benefícios do RH.
 *
 * Regra pura (sem infra-estrutura): recebe um Employee e um Benefit e decide
 * se o funcionário pode receber o benefício, considerando:
 *  - restrição a um cargo específico (position_id), quando definida;
 *  - restrição a um departamento específico (department_id), quando definida;
 *  - antiguidade mínima em meses (min_tenure_months) calculada a partir da
 *    data de contratação.
 */
final class BenefitPolicy
{
    public function isEligible(Employee $employee, Benefit $benefit, ?string $referenceDate = null): bool
    {
        return $this->violations($employee, $benefit, $referenceDate) === [];
    }

    /**
     * Motivos (em português) que impedem o funcionário de receber o benefício.
     *
     * @return array<int, string>
     */
    public function violations(Employee $employee, Benefit $benefit, ?string $referenceDate = null): array
    {
        $problems = [];

        if ($benefit->position_id !== null && $benefit->position_id !== $employee->position_id) {
            $problems[] = 'Benefício restrito a outro cargo.';
        }

        if ($benefit->department_id !== null && $benefit->department_id !== $employee->department_id) {
            $problems[] = 'Benefício restrito a outro departamento.';
        }

        $months = $this->completedMonths(
            (string) ($employee->hire_date?->format('Y-m-d') ?? ''),
            $referenceDate ?? date('Y-m-d')
        );

        if ($months < (int) $benefit->min_tenure_months) {
            $problems[] = sprintf(
                'Exige antiguidade mínima de %d %s.',
                (int) $benefit->min_tenure_months,
                (int) $benefit->min_tenure_months === 1 ? 'mês' : 'meses'
            );
        }

        return $problems;
    }

    private function completedMonths(string $from, string $to): int
    {
        if ($from === '') {
            return 0;
        }

        $fromDt = new \DateTimeImmutable($from);
        $toDt = new \DateTimeImmutable($to);

        if ($toDt < $fromDt) {
            return 0;
        }

        $diff = $fromDt->diff($toDt);

        return ($diff->y * 12) + $diff->m;
    }
}