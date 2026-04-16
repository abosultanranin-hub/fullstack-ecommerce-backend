<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserLoginIp;
use App\Models\SecurityVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecurityVerificationMail;

class SuspiciousLoginVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_from_new_ip_requires_verification()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $ip = '123.123.123.123';

        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);

        $response->assertStatus(403);
        $response->assertJson(['status' => 'verification_required']);

        $this->assertDatabaseHas('security_verifications', [
            'user_id' => $user->id,
            'ip' => $ip
        ]);

        Mail::assertSent(SecurityVerificationMail::class, function ($mail) use ($user, $ip) {
            return $mail->hasTo($user->email) && $mail->ip === $ip;
        });
    }

    /** @test */
    public function login_from_known_ip_succeeds()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $ip = '123.123.123.123';
        UserLoginIp::create([
            'user_id' => $user->id,
            'ip' => $ip,
            'last_used_at' => now()
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    /** @test */
    public function verifying_security_token_adds_ip_to_known_list()
    {
        $user = User::factory()->create();
        $ip = '123.123.123.123';
        $token = 'secure-random-token-64-characters-long-etc-etc-etc-etc-etc-etc-etc';

        SecurityVerification::create([
            'user_id' => $user->id,
            'token' => $token,
            'ip' => $ip,
            'expires_at' => now()->addMinutes(15)
        ]);

        $response = $this->getJson("/api/security/verify/{$token}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'تم التحقق من جهازك بنجاح. يمكنك الآن تسجيل الدخول.']);

        $this->assertDatabaseHas('user_login_ips', [
            'user_id' => $user->id,
            'ip' => $ip
        ]);

        $this->assertDatabaseMissing('security_verifications', ['token' => $token]);
    }

    /** @test */
    public function expired_token_fails_verification()
    {
        $user = User::factory()->create();
        $token = 'expired-token';

        SecurityVerification::create([
            'user_id' => $user->id,
            'token' => $token,
            'ip' => '1.1.1.1',
            'expires_at' => now()->subMinute()
        ]);

        $response = $this->getJson("/api/security/verify/{$token}");

        $response->assertStatus(400);
        $response->assertJson(['message' => 'رابط التحقق غير صالح أو منتهي الصلاحية']);
    }
}
