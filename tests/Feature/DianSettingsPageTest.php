<?php

namespace Tests\Feature;

use App\Filament\Pages\DianSettings;
use App\Filament\Resources\DianResolutions\DianResolutionResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DianSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dian_settings(): void
    {
        $this->get('/admin/configuracion-dian')->assertRedirect('/admin/login');
    }

    public function test_admin_can_open_dian_settings_and_save_company(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/configuracion-dian')
            ->assertOk();

        Livewire::actingAs($user)
            ->test(DianSettings::class)
            ->fillForm([
                'dian_company_nit' => '901111222',
                'dian_company_dv' => '3',
                'dian_company_razon_social' => 'Smart Tech Security S.A.S.',
                'dian_environment' => '2',
                'dian_enabled' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'key' => 'dian_company_nit',
            'value' => '901111222',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'dian_company_razon_social',
            'value' => 'Smart Tech Security S.A.S.',
        ]);
    }

    public function test_admin_can_open_resolutions_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(DianResolutionResource::getUrl('index'))
            ->assertOk();
    }
}
