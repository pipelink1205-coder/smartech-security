<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_exposes_unique_meta_canonical_and_local_business_schema(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<title>'.e(Seo::page('home')['title']).'</title>', false)
            ->assertSee('name="description" content="'.e(Seo::page('home')['description']).'"', false)
            ->assertSee('href="https://smarttechsecurity.com.co/"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"LocalBusiness"', false)
            ->assertSee('"@type":"WebSite"', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('og:locale', false);
    }

    public function test_core_pages_use_production_canonicals_and_unique_titles(): void
    {
        $pages = [
            '/servicios' => Seo::page('servicios'),
            '/proyectos' => Seo::page('proyectos'),
            '/contacto' => Seo::page('contacto'),
            '/privacidad' => Seo::page('privacidad'),
        ];

        foreach ($pages as $path => $meta) {
            $this->get($path)
                ->assertOk()
                ->assertSee('<title>'.e($meta['title']).'</title>', false)
                ->assertSee('name="description" content="'.e($meta['description']).'"', false)
                ->assertSee('href="https://smarttechsecurity.com.co'.$path.'"', false);
        }
    }

    public function test_generic_service_page_includes_service_schema_and_og_image(): void
    {
        $service = Service::query()->create([
            'name' => 'Cámaras de Seguridad 4K',
            'slug' => 'camaras-4k',
            'description' => 'Videovigilancia IP Ultra HD para hogares y empresas en Medellín.',
            'icon' => 'camera',
            'image' => 'images/services/camaras-4k.jpg',
            'is_active' => true,
            'show_on_home' => true,
            'order' => 1,
        ]);

        $this->get('/servicios/'.$service->slug)
            ->assertOk()
            ->assertSee('Cámaras de Seguridad 4K en Medellín | Smart Tech Security')
            ->assertSee('Videovigilancia IP Ultra HD')
            ->assertSee('https://smarttechsecurity.com.co/servicios/camaras-4k', false)
            ->assertSee('"@type":"Service"', false)
            ->assertSee('Cámaras de Seguridad 4K');
    }

    public function test_sitemap_uses_production_host_and_prioritizes_key_services(): void
    {
        Service::query()->create([
            'name' => 'Desarrollo de Software',
            'slug' => 'desarrollo-software',
            'description' => 'Software a la medida.',
            'icon' => 'code',
            'is_active' => true,
            'order' => 1,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('https://smarttechsecurity.com.co/', false)
            ->assertSee('https://smarttechsecurity.com.co/servicios', false)
            ->assertSee('https://smarttechsecurity.com.co/servicios/desarrollo-software', false)
            ->assertSee('<priority>0.9</priority>', false)
            ->assertDontSee('localhost', false);
    }

    public function test_seo_helper_rewrites_localhost_urls_to_production(): void
    {
        $this->assertSame('https://smarttechsecurity.com.co', Seo::siteUrl());
        $this->assertSame('https://smarttechsecurity.com.co/servicios', Seo::url('/servicios'));
        $this->assertSame(
            'https://smarttechsecurity.com.co/images/logo-transparent.png',
            Seo::url('http://localhost/images/logo-transparent.png')
        );
    }
}
