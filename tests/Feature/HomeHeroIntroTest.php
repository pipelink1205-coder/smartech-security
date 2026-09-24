<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeHeroIntroTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_keeps_hero_and_renders_the_progressive_intro_layer(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('class="home-hero-stage"', false)
            ->assertSee('data-hero-tech-intro', false)
            ->assertSee('data-force="false"', false)
            ->assertSee('images/logo.png', false)
            ->assertSee('Seguridad electrónica')
            ->assertSee('Desarrollo de Software')
            ->assertSee('Omitir intro')
            ->assertSee('data-page-link="servicios"', false)
            ->assertSee(config('site.hero.title'));
    }
}
