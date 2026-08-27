<?php

namespace Tests\Feature;

use App\Filament\Resources\WhatsappLeads\WhatsappLeadResource;
use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
use App\Models\WhatsappLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WhatsappLeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_stores_lead_and_returns_whatsapp_url(): void
    {
        Mail::fake();
        $this->seedService();

        $response = $this->postJson(route('whatsapp-leads.store'), [
            'name' => 'María López',
            'phone' => '300 111 2233',
            'service' => 'Cámaras de Seguridad 4K',
            'message' => 'Quiero 8 cámaras para la casa',
            'source' => 'fab',
            'page_url' => 'http://localhost/servicios/camaras-4k',
            'page_title' => 'Cámaras 4K',
            'destination' => config('contact.whatsapp'),
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonStructure(['whatsapp_url']);

        $lead = WhatsappLead::query()->first();
        $this->assertNotNull($lead);
        $this->assertSame('María López', $lead->name);
        $this->assertSame('573001112233', $lead->phone);
        $this->assertSame('Cámaras de Seguridad 4K', $lead->service);
        $this->assertSame('fab', $lead->source);
        $this->assertSame(1, $lead->click_count);
        $this->assertStringContainsString('wa.me/', $response->json('whatsapp_url'));

        Mail::assertNothingSent();
    }

    public function test_rejects_invalid_payload(): void
    {
        $this->postJson(route('whatsapp-leads.store'), [
            'name' => 'Al',
            'phone' => 'abc',
            'service' => 'Servicio inventado',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'service']);

        $this->assertSame(0, WhatsappLead::query()->count());
    }

    public function test_honeypot_does_not_store_a_lead(): void
    {
        $this->postJson(route('whatsapp-leads.store'), [
            'name' => 'Bot',
            'phone' => '3001112233',
            'service' => 'Varios servicios',
            'website' => 'https://spam.example',
        ])->assertOk();

        $this->assertSame(0, WhatsappLead::query()->count());
    }

    public function test_same_phone_increments_clicks(): void
    {
        $this->seedService();

        $payload = [
            'name' => 'Carlos Ruiz',
            'phone' => '3001112233',
            'service' => 'Cámaras de Seguridad 4K',
            'source' => 'fab',
        ];

        $this->postJson(route('whatsapp-leads.store'), $payload)->assertOk();
        $this->postJson(route('whatsapp-leads.store'), $payload + ['source' => 'footer'])->assertOk();

        $this->assertSame(1, WhatsappLead::query()->count());
        $this->assertSame(2, WhatsappLead::query()->first()->click_count);
    }

    public function test_converts_lead_into_quote(): void
    {
        $lead = WhatsappLead::create([
            'name' => 'Ana Pérez',
            'phone' => '573001112233',
            'service' => 'Energía Solar',
            'message' => 'Quiero paneles para el techo',
            'source' => 'service_hero',
            'status' => 'new',
        ]);

        $quote = $lead->toQuote();

        $this->assertInstanceOf(Quote::class, $quote);
        $this->assertSame('Ana Pérez', $quote->name);
        $this->assertSame('573001112233', $quote->phone);
        $this->assertSame('Energía Solar', $quote->service);
        $this->assertSame($quote->id, $lead->fresh()->quote_id);
        $this->assertSame('quoted', $lead->fresh()->status);
        $this->assertSame($quote->id, $lead->toQuote()->id);
    }

    public function test_admin_can_list_whatsapp_leads(): void
    {
        $user = User::factory()->create();
        $lead = WhatsappLead::create([
            'name' => 'Visitante',
            'phone' => '573001112233',
            'service' => 'Varios servicios',
            'source' => 'fab',
            'click_count' => 3,
            'status' => 'new',
        ]);

        $this->actingAs($user)
            ->get(WhatsappLeadResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Visitante')
            ->assertSee('3');

        $this->actingAs($user)
            ->get(WhatsappLeadResource::getUrl('edit', ['record' => $lead]))
            ->assertOk()
            ->assertSee('Visitante');
    }

    private function seedService(): void
    {
        Service::query()->create([
            'name' => 'Cámaras de Seguridad 4K',
            'slug' => 'camaras-4k',
            'description' => 'Prueba',
            'icon' => '📹',
            'is_active' => true,
            'order' => 1,
        ]);
    }
}
