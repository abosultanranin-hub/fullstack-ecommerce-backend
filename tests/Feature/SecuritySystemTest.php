<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BlockedEmailDomain;
use App\Models\BlockedIp;
use App\Models\BlockedCountry;
use App\Models\SecurityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SecuritySystemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_blocks_prohibited_email_domains_during_registration()
    {
        BlockedEmailDomain::create(['domain' => 'prohibited.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@prohibited.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'نطاق البريد الإلكتروني هذا محظور']);
    }

    /** @test */
    public function it_blocks_ips_after_multiple_failed_logins()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct_password')
        ]);

        $ip = '192.168.1.1';

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])
                 ->postJson('/api/login', [
                     'email' => 'test@example.com',
                     'password' => 'wrong_password'
                 ]);
        }

        // Check if IP is blocked in database
        $this->assertTrue(BlockedIp::where('ip', $ip)->exists());

        // 6th attempt should be blocked by middleware
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                         ->postJson('/api/login', [
                             'email' => 'test@example.com',
                             'password' => 'correct_password'
                         ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'تم حظر IP الخاص بك مؤقتاً']);
    }

    /** @test */
    public function it_blocks_prohibited_countries()
    {
        BlockedCountry::create(['country_code' => 'PS']); // 'PS' is default for local in GeoIPService

        $response = $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
                         ->postJson('/api/login', [
                             'email' => 'test@example.com',
                             'password' => 'any'
                         ]);

        $response->assertStatus(403);
        // The middleware uses abort(403, '...') which might return HTML or JSON depending on headers
        // But since it's an API route it should be JSON if Accept: application/json is sent
    }
}
