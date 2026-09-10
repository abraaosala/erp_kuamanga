<?php

declare(strict_types=1);

use App\Support\ValorExtenso;

it('converts whole numbers', function (int $number, string $expected): void {
    $extenso = new ValorExtenso();

    expect($extenso->number($number))->toBe($expected);
})->with([
    [0, 'zero'],
    [1, 'um'],
    [15, 'quinze'],
    [21, 'vinte e um'],
    [45, 'quarenta e cinco'],
    [100, 'cem'],
    [101, 'cento e um'],
    [217, 'duzentos e dezassete'],
    [1000, 'mil'],
    [2000, 'dois mil'],
    [1500, 'mil e quinhentos'],
    [505000, 'quinhentos e cinco mil'],
    [1000000, 'um milhão'],
    [1234567, 'um milhão e duzentos e trinta e quatro mil e quinhentos e sessenta e sete'],
    [1000000000, 'mil milhões'],
]);

it('converts money values', function (): void {
    $extenso = new ValorExtenso();

    expect($extenso->money(243370.0))->toBe('duzentos e quarenta e três mil e trezentos e setenta kwanzas');
    expect($extenso->money(1.5))->toBe('um kwanza e cinquenta cêntimos');
    expect($extenso->money(0.05))->toBe('zero kwanzas e cinco cêntimos');
    expect($extenso->money(1.01))->toBe('um kwanza e um cêntimo');
    expect($extenso->money(-100.0))->toBe('menos cem kwanzas');
});