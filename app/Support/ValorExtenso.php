<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

/**
 * Converte valores monetários em texto por extenso (português de Angola).
 */
class ValorExtenso
{
    /** @var array<int, string> */
    private const UNIDADES = [
        0 => 'zero', 1 => 'um', 2 => 'dois', 3 => 'três', 4 => 'quatro', 5 => 'cinco',
        6 => 'seis', 7 => 'sete', 8 => 'oito', 9 => 'nove', 10 => 'dez', 11 => 'onze',
        12 => 'doze', 13 => 'treze', 14 => 'catorze', 15 => 'quinze', 16 => 'dezasseis',
        17 => 'dezassete', 18 => 'dezoito', 19 => 'dezanove',
    ];

    /** @var array<int, string> */
    private const DEZENAS = [
        0 => '', 2 => 'vinte', 3 => 'trinta', 4 => 'quarenta', 5 => 'cinquenta',
        6 => 'sessenta', 7 => 'setenta', 8 => 'oitenta', 9 => 'noventa',
    ];

    /** @var array<int, string> */
    private const CENTENAS = [
        1 => 'cento', 2 => 'duzentos', 3 => 'trezentos', 4 => 'quatrocentos',
        5 => 'quinhentos', 6 => 'seiscentos', 7 => 'setecentos', 8 => 'oitocentos',
        9 => 'novecentos',
    ];

    public function money(float $value): string
    {
        $abs = abs($value);
        $whole = (int) floor($abs);
        $cents = (int) round(($abs - $whole) * 100);

        $text = $this->number($whole) . ($whole === 1 ? ' kwanza' : ' kwanzas');
        if ($cents > 0) {
            $text .= ' e ' . $this->number($cents) . ($cents === 1 ? ' cêntimo' : ' cêntimos');
        }

        return $value < 0 ? 'menos ' . $text : $text;
    }

    public function number(int $number): string
    {
        if ($number < 0) {
            return 'menos ' . $this->number(-$number);
        }

        if ($number < 20) {
            return self::UNIDADES[$number];
        }

        if ($number < 100) {
            return $this->underHundred($number);
        }

        if ($number < 1000) {
            return $this->underThousand($number);
        }

        if ($number < 1000000) {
            return $this->withGroup($number, 1000, 'mil', 'mil');
        }

        if ($number < 1000000000) {
            return $this->withGroup($number, 1000000, 'um milhão', 'milhões');
        }

        return $this->withGroup($number, 1000000000, 'mil milhões', 'mil milhões');
    }

    private function underHundred(int $number): string
    {
        if ($number < 20) {
            return self::UNIDADES[$number];
        }

        $tens = intdiv($number, 10);
        $ones = $number % 10;

        if ($ones === 0) {
            return self::DEZENAS[$tens];
        }

        return self::DEZENAS[$tens] . ' e ' . self::UNIDADES[$ones];
    }

    private function underThousand(int $number): string
    {
        if ($number === 100) {
            return 'cem';
        }

        $hundreds = intdiv($number, 100);
        $rest = $number % 100;

        if ($rest === 0) {
            return self::CENTENAS[$hundreds];
        }

        return self::CENTENAS[$hundreds] . ' e ' . $this->underHundred($rest);
    }

    private function withGroup(int $number, int $unit, string $singular, string $plural): string
    {
        $count = intdiv($number, $unit);
        $rest = $number % $unit;

        $group = $count === 1 ? $singular : $this->number($count) . ' ' . $plural;

        if ($rest === 0) {
            return $group;
        }

        return $group . ' e ' . $this->number($rest);
    }
}
