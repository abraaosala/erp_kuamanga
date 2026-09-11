<?php

declare(strict_types=1);

namespace App\Services\Modules\Rh;

/**
 * Calcula o IRT (Grupo A — trabalhadores por conta de outrem) de Angola
 * conforme a tabela em vigor para 2026 (OGE 2026, Lei n.º 14/25).
 */
class IrtCalculator
{
    public const SOCIAL_SECURITY_RATE = 0.03;

    /**
     * Escalões: [limite_inferior, limite_superior, parcela_fixa, taxa, excesso]
     *
     * @var array<int, array{0: float, 1: float, 2: float, 3: float, 4: float}>
     */
    private const BRACKETS = [
        [0.0, 150000.0, 0.0, 0.0, 0.0],
        [150000.0, 200000.0, 12500.0, 0.16, 150000.0],
        [200000.0, 300000.0, 31250.0, 0.18, 200000.0],
        [300000.0, 500000.0, 49250.0, 0.19, 300000.0],
        [500000.0, 1000000.0, 87250.0, 0.20, 500000.0],
        [1000000.0, 1500000.0, 187250.0, 0.21, 1000000.0],
        [1500000.0, 2000000.0, 292250.0, 0.22, 1500000.0],
        [2000000.0, 2500000.0, 402250.0, 0.23, 2000000.0],
        [2500000.0, 5000000.0, 517250.0, 0.24, 2500000.0],
        [5000000.0, 10000000.0, 1117250.0, 0.245, 5000000.0],
        [10000000.0, PHP_FLOAT_MAX, 2342250.0, 0.25, 10000000.0],
    ];

    public function tax(float $monthlyIncome): float
    {
        if ($monthlyIncome <= 0.0) {
            return 0.0;
        }

        foreach (self::BRACKETS as $bracket) {
            [$lower, $upper, $fixed, $rate, $excess] = $bracket;
            if ($monthlyIncome > $lower && $monthlyIncome <= $upper) {
                return $fixed + (($monthlyIncome - $excess) * $rate);
            }
        }

        return 0.0;
    }

    public function socialSecurity(float $gross): float
    {
        return round($gross * self::SOCIAL_SECURITY_RATE, 2);
    }
}
