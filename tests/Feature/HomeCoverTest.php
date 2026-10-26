<?php

namespace Tests\Feature;

use App\Filament\Resources\HomeCovers\Pages\CreateHomeCover;
use App\Filament\Resources\HomeCovers\Pages\ListHomeCovers;
use App\Models\HomeCover;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class HomeCoverTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_falls_back_to_site_config_when_no_covers_exist(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(config('site.hero.title'))
            ->assertSee(config('site.hero.badge'))
            ->assertSee('data-page-link="servicios"', false)
            ->assertSee('data-quote-intent="visit"', false);
    }

    public function test_published_campaign_wins_over_institutional_fallback(): void
    {
        $this->seedInstitutional();

        HomeCover::query()->create([
            'name' => 'Campaña solar',
            'slug' => 'campana-solar',
            'kind' => HomeCover::KIND_CAMPANA,
            'badge' => 'Promoción octubre',
            'title' => 'Ahorra con energía solar este mes',
            'subtitle' => 'Diagnóstico gratuito para hogares y empresas.',
            'cta_primary_label' => 'Ver energía solar',
            'cta_primary_url' => '#servicios',
            'cta_secondary_label' => 'Agendar visita',
            'cta_secondary_url' => '/?intent=visit#contacto',
            'is_published' => true,
            'is_fallback' => false,
            'priority' => 20,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDay(),
        ]);

        Cache::forget(HomeCover::CACHE_KEY);

        $this->get('/')
            ->assertOk()
            ->assertSee('Ahorra con energía solar este mes')
            ->assertSee('Promoción octubre')
            ->assertSee('Diagnóstico gratuito para hogares y empresas.');
    }

    public function test_expired_campaign_returns_to_fallback(): void
    {
        $this->seedInstitutional();

        HomeCover::query()->create([
            'name' => 'Campaña vencida',
            'slug' => 'campana-vencida',
            'kind' => HomeCover::KIND_PROMOCION,
            'title' => 'Oferta que ya terminó',
            'cta_primary_label' => 'Ver Servicios',
            'cta_primary_url' => '#servicios',
            'cta_secondary_label' => 'Agendar visita gratuita',
            'cta_secondary_url' => '/?intent=visit#contacto',
            'is_published' => true,
            'priority' => 50,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);

        Cache::forget(HomeCover::CACHE_KEY);

        $this->get('/')
            ->assertOk()
            ->assertSee(config('site.hero.title'))
            ->assertDontSee('Oferta que ya terminó');
    }

    public function test_admin_can_preview_unpublished_cover(): void
    {
        $this->seedInstitutional();
        $user = User::factory()->create();

        $draft = HomeCover::query()->create([
            'name' => 'Borrador aire nuevo',
            'slug' => 'aire-nuevo',
            'kind' => HomeCover::KIND_AIRE,
            'title' => 'Nueva portada de prueba',
            'cta_primary_label' => 'Ver Servicios',
            'cta_primary_url' => '#servicios',
            'cta_secondary_label' => 'Agendar visita gratuita',
            'cta_secondary_url' => '/?intent=visit#contacto',
            'is_published' => false,
        ]);

        $this->get('/?preview_portada='.$draft->id)
            ->assertOk()
            ->assertDontSee('Nueva portada de prueba');

        $this->actingAs($user)
            ->get('/?preview_portada='.$draft->id)
            ->assertOk()
            ->assertSee('Nueva portada de prueba')
            ->assertSee('Vista previa · Borrador aire nuevo');
    }

    public function test_only_one_fallback_remains_when_another_is_saved(): void
    {
        $first = $this->seedInstitutional();

        $second = HomeCover::query()->create([
            'name' => 'Nuevo respaldo',
            'slug' => 'nuevo-respaldo',
            'kind' => HomeCover::KIND_INSTITUCIONAL,
            'title' => 'Respaldo nuevo',
            'cta_primary_label' => 'Ver Servicios',
            'cta_primary_url' => '#servicios',
            'cta_secondary_label' => 'Agendar visita gratuita',
            'cta_secondary_url' => '/?intent=visit#contacto',
            'is_published' => true,
            'is_fallback' => true,
        ]);

        $this->assertFalse($first->fresh()->is_fallback);
        $this->assertTrue($second->fresh()->is_fallback);
    }

    public function test_admin_can_open_portadas_module(): void
    {
        $user = User::factory()->create();
        $this->seedInstitutional();

        $this->actingAs($user)
            ->get('/admin/portadas')
            ->assertOk()
            ->assertSee('Portadas')
            ->assertSee('Portada institucional');

        Livewire::actingAs($user)
            ->test(ListHomeCovers::class)
            ->assertSee('Portada institucional');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();

        Livewire::actingAs($user)
            ->test(CreateHomeCover::class)
            ->assertSuccessful();
    }

    private function seedInstitutional(): HomeCover
    {
        $cover = HomeCover::fromSiteConfig();

        return HomeCover::query()->create([
            'name' => $cover->name,
            'slug' => $cover->slug,
            'kind' => $cover->kind,
            'badge' => $cover->badge,
            'title' => $cover->title,
            'subtitle' => $cover->subtitle,
            'image' => $cover->image,
            'image_alt' => $cover->image_alt,
            'cta_primary_label' => $cover->cta_primary_label,
            'cta_primary_url' => $cover->cta_primary_url,
            'cta_secondary_label' => $cover->cta_secondary_label,
            'cta_secondary_url' => $cover->cta_secondary_url,
            'strip_label' => $cover->strip_label,
            'strip_highlights' => $cover->strip_highlights,
            'stats' => $cover->stats,
            'is_published' => true,
            'is_fallback' => true,
            'priority' => 0,
        ]);
    }
}
