<?php

namespace Tests\Feature;

use App\Models\DianResolution;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DianResolutionNumberingTest extends TestCase
{
    use RefreshDatabase;

    public function test_next_number_starts_at_range_and_increments(): void
    {
        Setting::setValue('dian_environment', '2');

        DianResolution::create([
            'environment' => 2,
            'numero_resolucion' => '18760000001',
            'prefijo' => 'SETP',
            'rango_desde' => 990000000,
            'rango_hasta' => 995000000,
            'consecutivo_actual' => 0,
            'is_active' => true,
        ]);

        $first = DianResolution::nextNumber();
        $second = DianResolution::nextNumber();

        $this->assertSame('SETP', $first['prefijo']);
        $this->assertSame(990000000, $first['numero']);
        $this->assertSame(990000001, $second['numero']);
    }

    public function test_activating_a_resolution_deactivates_others_in_the_same_environment(): void
    {
        $first = DianResolution::create([
            'environment' => 2,
            'numero_resolucion' => 'AAA',
            'prefijo' => 'SETP',
            'rango_desde' => 1,
            'rango_hasta' => 10,
            'is_active' => true,
        ]);

        DianResolution::create([
            'environment' => 2,
            'numero_resolucion' => 'BBB',
            'prefijo' => 'SETP',
            'rango_desde' => 11,
            'rango_hasta' => 20,
            'is_active' => true,
        ]);

        $this->assertFalse($first->fresh()->is_active);
        $this->assertSame('BBB', DianResolution::active()?->numero_resolucion);
    }

    public function test_next_number_fails_without_active_resolution(): void
    {
        Setting::setValue('dian_environment', '2');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No hay una resolución DIAN activa');

        DianResolution::nextNumber();
    }

    public function test_next_number_fails_when_range_is_exhausted(): void
    {
        Setting::setValue('dian_environment', '2');

        DianResolution::create([
            'environment' => 2,
            'numero_resolucion' => 'FULL',
            'prefijo' => 'SETP',
            'rango_desde' => 1,
            'rango_hasta' => 1,
            'consecutivo_actual' => 1,
            'is_active' => true,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Rango de numeración DIAN agotado');

        DianResolution::nextNumber();
    }
}
