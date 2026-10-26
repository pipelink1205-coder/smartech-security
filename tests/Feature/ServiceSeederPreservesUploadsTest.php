<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceSeederPreservesUploadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_does_not_overwrite_uploaded_image_or_visibility(): void
    {
        Service::query()->create([
            'name' => 'Cámaras de Seguridad 4K',
            'slug' => 'camaras-4k',
            'description' => 'Foto subida en producción.',
            'icon' => 'camera',
            'image' => 'images/services/mi-foto-produccion.jpg',
            'is_active' => false,
            'show_on_home' => false,
            'order' => 1,
        ]);

        $this->seed(ServiceSeeder::class);

        $service = Service::query()->where('slug', 'camaras-4k')->first();

        $this->assertNotNull($service);
        $this->assertSame('images/services/mi-foto-produccion.jpg', $service->image);
        $this->assertFalse($service->is_active);
        $this->assertFalse($service->show_on_home);
    }
}
