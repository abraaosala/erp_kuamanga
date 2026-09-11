<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

/**
 * Regras legais de férias em Angola (LGT — Lei n.º 7/15, de 15 de Junho,
 * revogada pela Lei n.º 12/23) aplicadas ao cálculo do direito por antiguidade:
 *
 * - Após 1 ano de serviço efectivo: 22 dias úteis de férias por ano.
 * - No primeiro ano (admitidos durante o ano): 2 dias úteis por cada mês
 *   completo de trabalho, com o limite máximo de 22 dias e mínimo de 6 dias
 *   relativamente a esse ano.
 *
 * Regra pura (sem infra-estrutura): recebe datas em 'Y-m-d' e devolve dias.
 */
final class LeavePolicy
{
    public const DAYS_PER_YEAR = 22;

    public const DAYS_PER_FULL_MONTH = 2;

    public const MIN_FIRST_YEAR_DAYS = 6;

    /**
     * Direito anual de férias (dias por antiguidade) na data de referência.
     */
    public function entitledDaysFor(string $hireDate, ?string $referenceDate = null): int
    {
        $ref = $referenceDate ?? date('Y-m-d');

        if (strtotime($hireDate) > strtotime($ref)) {
            return 0;
        }

        $months = $this->completedMonths($hireDate, $ref);
        if ($months <= 0) {
            return 0;
        }

        if ($months >= 12) {
            return self::DAYS_PER_YEAR;
        }

        $proportional = $months * self::DAYS_PER_FULL_MONTH;

        return min(self::DAYS_PER_YEAR, max(self::MIN_FIRST_YEAR_DAYS, $proportional));
    }

    /**
     * Dias de um período de férias (inclusive).
     */
    public function inclusiveDays(string $start, string $end): int
    {
        $startTs = strtotime($start);
        $endTs = strtotime($end);

        if ($startTs === false || $endTs === false || $endTs < $startTs) {
            throw new \InvalidArgumentException('Período de férias inválido: a data final deve ser igual ou posterior à inicial.');
        }

        return (int) (($endTs - $startTs) / 86400) + 1;
    }

    private function completedMonths(string $from, string $to): int
    {
        $fromDt = new \DateTimeImmutable($from);
        $toDt = new \DateTimeImmutable($to);

        if ($toDt < $fromDt) {
            return 0;
        }

        $diff = $fromDt->diff($toDt);

        return ($diff->y * 12) + $diff->m;
    }
}