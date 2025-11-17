<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test the health check endpoint.
     */
    public function test_health_check_endpoint_returns_ok(): void
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'service' => 'ecommerce-platform',
            ]);
    }
}
