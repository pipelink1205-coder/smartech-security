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
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'page_title' => 'Inicio',
            'status' => 'new',
        ]);

        $this->actingAs($user)
            ->get(WhatsappLeadResource::getUrl('index'))
            ->assertOk()
            ->assertSee('201.184.10.20')
            ->assertSee('Botón flotante')
            ->assertSee('Persona')
            ->assertSee('Chrome');
    }

    public function test_saves_real_visitor_ip_from_cloudflare_header(): void
    {
        $this->withHeaders([
            'CF-Connecting-IP' => '181.49.10.20',
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Mobile Safari/537.36',
        ])->get(route('whatsapp.click', ['from' => 'fab']))->assertRedirect();

        $click = WhatsappLead::query()->first();
        $this->assertSame('181.49.10.20', $click?->ip);
        $this->assertSame('person', $click?->visitorKind());
        $this->assertFalse($click?->ipIsCloudflare());
    }

    public function test_classifies_crawler_user_agent_as_bot(): void
    {
        $click = new WhatsappLead([
            'user_agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'ip' => '66.249.66.1',
        ]);

        $this->assertSame('bot', $click->visitorKind());
        $this->assertSame('Googlebot', $click->browserLabel());
    }

    public function test_cloudflare_edge_ip_is_labeled(): void
    {
        $click = new WhatsappLead([
            'ip' => '104.22.93.80',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ]);

        $this->assertTrue($click->ipIsCloudflare());
        $this->assertSame('person', $click->visitorKind());
        $this->assertSame('Safari', $click->browserLabel());
    }
}
