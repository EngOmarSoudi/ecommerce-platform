<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaTest extends TestCase
{
    /** @test */
    public function manifest_json_is_accessible()
    {
        $response = $this->get('/manifest.json');
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    /** @test */
    public function manifest_contains_required_fields()
    {
        $response = $this->get('/manifest.json');
        
        $manifest = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('name', $manifest);
        $this->assertArrayHasKey('short_name', $manifest);
        $this->assertArrayHasKey('start_url', $manifest);
        $this->assertArrayHasKey('display', $manifest);
        $this->assertArrayHasKey('icons', $manifest);
        $this->assertEquals('standalone', $manifest['display']);
    }

    /** @test */
    public function service_worker_is_accessible()
    {
        $response = $this->get('/sw.js');
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/javascript');
    }

    /** @test */
    public function offline_page_is_accessible()
    {
        $response = $this->get('/offline.html');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function pwa_meta_tags_are_present()
    {
        $response = $this->get('/');
        
        $response->assertSee('<meta name="mobile-web-app-capable"', false);
        $response->assertSee('<meta name="apple-mobile-web-app-capable"', false);
        $response->assertSee('<meta name="theme-color"', false);
        $response->assertSee('<link rel="manifest"', false);
    }

    /** @test */
    public function pwa_icons_paths_are_correct()
    {
        $response = $this->get('/manifest.json');
        
        $manifest = json_decode($response->getContent(), true);
        
        $this->assertNotEmpty($manifest['icons']);
        
        foreach ($manifest['icons'] as $icon) {
            $this->assertArrayHasKey('src', $icon);
            $this->assertArrayHasKey('sizes', $icon);
            $this->assertArrayHasKey('type', $icon);
        }
    }

    /** @test */
    public function app_shortcuts_are_configured()
    {
        $response = $this->get('/manifest.json');
        
        $manifest = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('shortcuts', $manifest);
        $this->assertNotEmpty($manifest['shortcuts']);
    }
}
