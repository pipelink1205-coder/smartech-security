<?php

namespace Tests\Feature;

use App\Filament\Resources\WhatsappLeads\WhatsappLeadResource;
use App\Models\User;
use App\Models\WhatsappLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappLeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_clicking_whatsapp_link_is_logged_and_opens_chat(): void
    {
        $this->get(route('whatsapp.click', ['from' => 'fab']))
            ->assertRedirect('https://wa.me/'.config('contact.whatsapp'));

        $click = WhatsappLead::query()->first();
        $this->assertNotNull($click);
        $this->assertSame('fab', $click->source);
        $this->assertNotNull($click->created_at);
    }

    public function test_click_is_logged_without_asking_for_data(): void
    {
        $this->postJson(route('whatsapp-leads.store'), [
            'source' => 'fab',
            'page_url' => 'http://localhost/servicios/camaras-4k',
            'page_title' => 'Cámaras 4K',
        ])->assertOk()->assertJsonPath('ok', true);

        $click = WhatsappLead::query()->first();
        $this->assertNotNull($click);
        $this->assertSame('fab', $click->source);
        $this->assertSame('Cámaras 4K', $click->page_title);
        $this->assertSame('', $click->name);
        $this->assertSame('', $click->phone);
        $this->assertNotNull($click->created_at);
    }

    public function test_each_click_creates_its_own_row(): void
    {
        $this->postJson(route('whatsapp-leads.store'), ['source' => 'fab'])->assertOk();
        $this->postJson(route('whatsapp-leads.store'), ['source' => 'footer'])->assertOk();

        $this->assertSame(2, WhatsappLead::query()->count());
    }

    public function test_admin_can_see_click_log(): void
    {
        $user = User::factory()->create();
        WhatsappLead::create([
            'name' => '',
            'phone' => '',
            'source' => 'fab',
            'ip' => '201.184.10.20',
            'page_title' => 'Inicio',
            'status' => 'new',
        ]);

        $this->actingAs($user)
            ->get(WhatsappLeadResource::getUrl('index'))
            ->assertOk()
            ->assertSee('201.184.10.20')
            ->assertSee('Botón flotante');
    }
}
