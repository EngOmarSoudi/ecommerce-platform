<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GdprComplianceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GdprComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected GdprComplianceService $gdprService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gdprService = new GdprComplianceService();
        Storage::fake('local');
    }

    /** @test */
    public function user_data_can_be_exported()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $exportPath = $this->gdprService->exportUserData($user);

        $this->assertFileExists($exportPath);
        
        $content = file_get_contents($exportPath);
        $data = json_decode($content, true);

        $this->assertEquals('John Doe', $data['personal_information']['name']);
        $this->assertEquals('john@example.com', $data['personal_information']['email']);
    }

    /** @test */
    public function user_data_can_be_anonymized()
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
        ]);

        $result = $this->gdprService->anonymizeUser($user);

        $this->assertTrue($result);
        
        $user->refresh();
        
        $this->assertStringStartsWith('Deleted User #', $user->name);
        $this->assertStringContains('@anonymized.local', $user->email);
        $this->assertNull($user->phone);
    }

    /** @test */
    public function user_account_can_be_deleted()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $result = $this->gdprService->deleteUserAccount($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /** @test */
    public function data_retention_status_can_be_checked()
    {
        $status = $this->gdprService->getRetentionStatus();

        $this->assertIsArray($status);
        $this->assertArrayHasKey('orders', $status);
        $this->assertArrayHasKey('user_data', $status);
        $this->assertArrayHasKey('logs', $status);
        $this->assertArrayHasKey('sessions', $status);
    }

    /** @test */
    public function old_orders_are_marked_for_cleanup()
    {
        // Create old order (8 years ago, exceeds 7-year retention)
        $oldOrder = \App\Models\Order::factory()->create([
            'created_at' => now()->subYears(8),
        ]);

        $status = $this->gdprService->getRetentionStatus();

        $this->assertTrue($status['orders']['requires_cleanup']);
    }
}
