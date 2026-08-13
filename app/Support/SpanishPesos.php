<?php

namespace App\Support;

final class SpanishPesos
{
    /**
     * Convierte un valor COP a texto para cuentas de cobro.
     * Ej.: 447000 → "CUATROCIENTOS CUARENTA Y SIETE MIL PESOS M/CTE"
     */
    public static function fromAmount(float|int $amount): string
    {
        $pesos = (int) round(abs($amount));
        if ($pesos === 1) {
            return mb_strtoupper('un peso m/cte', 'UTF-8');
        }

        $words = self::integer($pesos);
        $needsDe = $pesos >= 1_000_000 && ($pesos % 1_000_000 === 0);
        $suffix = $needsDe ? ' de pesos m/cte' : ' pesos m/cte';

        return mb_strtoupper($words.$suffix, 'UTF-8');
    }

    public static function integer(int $n): string
    {
        if ($n === 0) {
            return 'cero';
        }

        if ($n < 0) {
            return 'menos '.self::integer(abs($n));
        }

        if ($n >= 1_000_000_000) {
            $billions = intdiv($n, 1_000_000_000);
            $rest = $n % 1_000_000_000;
            $head = $billions === 1 ? 'mil millones' : self::belowMillion($billions).' mil millones';

            return $rest === 0 ? $head : $head.' '.self::integer($rest);
        }

        if ($n >= 1_000_000) {
            $millions = intdiv($n, 1_000_000);
            $rest = $n % 1_000_000;
            $head = $millions === 1 ? 'un millón' : self::belowMillion($millions).' millones';

            return $rest === 0 ? $head : $head.' '.self::integer($rest);
        }

        return self::belowMillion($n);
    }

    private static function belowMillion(int $n): string
    {
        if ($n < 1000) {
            return self::belowThousand($n);
        }

        $thousands = intdiv($n, 1000);
        $rest = $n % 1000;
        $head = $thousands === 1 ? 'mil' : self::belowThousand($thousands, forThousands: true).' mil';

        return $rest === 0 ? $head : $head.' '.self::belowThousand($rest);
    }

    private static function belowThousand(int $n, bool $forThousands = false): string
    {
        if ($n < 100) {
            return self::belowHundred($n, $forThousands);
        }

        if ($n === 100) {
            return 'cien';
        }

        $hundreds = intdiv($n, 100);
        $rest = $n % 100;
        $map = [
            1 => 'ciento',
            2 => 'doscientos',
            3 => 'trescientos',
            4 => 'cuatrocientos',
            5 => 'quinientos',
            6 => 'seiscientos',
            7 => 'setecientos',
            8 => 'ochocientos',
            9 => 'novecientos',
        ];

        $head = $map[$hundreds];

        return $rest === 0 ? $head : $head.' '.self::belowHundred($rest, $forThousands);
    }

    private static function belowHundred(int $n, bool $forThousands = false): string
    {
        $units = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $special = [
            10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince',
            16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve',
            20 => 'veinte', 21 => 'veintiuno', 22 => 'veintidós', 23 => 'veintitrés', 24 => 'veinticuatro',
            25 => 'veinticinco', 26 => 'veintiséis', 27 => 'veintisiete', 28 => 'veintiocho', 29 => 'veintinueve',
        ];

        if ($n < 10) {
            $word = $units[$n];
            if ($forThousands && $n === 1) {
                return 'un';
            }

            return $word;
        }

        if (isset($special[$n])) {
            if ($forThousands && $n === 21) {
                return 'veintiún';
            }

            return $special[$n];
        }

        $tensMap = [
            30 => 'treinta', 40 => 'cuarenta', 50 => 'cincuenta',
            60 => 'sesenta', 70 => 'setenta', 80 => 'ochenta', 90 => 'noventa',
        ];
        $tens = intdiv($n, 10) * 10;
        $unit = $n % 10;
        $unitWord = $forThousands && $unit === 1 ? 'un' : $units[$unit];

        return $tensMap[$tens].' y '.$unitWord;
    }
}
