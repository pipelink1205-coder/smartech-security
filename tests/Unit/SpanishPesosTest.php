<?php

namespace Tests\Unit;

use App\Support\SpanishPesos;
use Tests\TestCase;

class SpanishPesosTest extends TestCase
{
    public function test_converts_typical_collection_amount(): void
    {
        $this->assertSame(
            'CUATROCIENTOS CUARENTA Y SIETE MIL PESOS M/CTE',
            SpanishPesos::fromAmount(447000),
        );
    }

    public function test_converts_one_peso_and_millions(): void
    {
        $this->assertSame('UN PESO M/CTE', SpanishPesos::fromAmount(1));
        $this->assertSame('UN MILLÓN DE PESOS M/CTE', SpanishPesos::fromAmount(1_000_000));
        $this->assertSame('DOS MILLONES TRESCIENTOS MIL PESOS M/CTE', SpanishPesos::fromAmount(2_300_000));
    }
}
