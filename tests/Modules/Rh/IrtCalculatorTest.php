<?php

declare(strict_types=1);

use App\Services\Modules\Rh\IrtCalculator;

it('applies social security rate to gross', function (): void {
    $calculator = new IrtCalculator();

    expect($calculator->socialSecurity(300000.0))->toBe(9000.0);
    expect($calculator->socialSecurity(0.0))->toBe(0.0);
});

it('is exempt up to the first bracket', function (): void {
    $calculator = new IrtCalculator();

    expect($calculator->tax(150000.0))->toBe(0.0);
    expect($calculator->tax(0.0))->toBe(0.0);
});

it('computes tax on bracket boundaries', function (float $income, float $expected): void {
    $calculator = new IrtCalculator();

    expect($calculator->tax($income))->toBe($expected);
})->with([
    'bracket 2 upper'  => [200000.0, 20500.0],
    'bracket 3 upper'  => [300000.0, 49250.0],
    'bracket 7 upper'  => [2000000.0, 402250.0],
    'bracket 11 lower' => [10000000.0, 2342250.0],
]);

it('computes tax on mid-bracket values', function (float $income, float $expected): void {
    $calculator = new IrtCalculator();

    expect($calculator->tax($income))->toBe($expected);
})->with([
    'bracket 2 mid' => [194000.0, 19540.0],
    'bracket 3 mid' => [291000.0, 47630.0],
    'bracket 7 mid' => [1900000.0, 380250.0],
    'bracket 11 mid'=> [15000000.0, 3592250.0],
]);