<?php

namespace Tests\Unit;

use App\Models\PortalOtp;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OtpService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OtpService;
        Mail::fake();
    }

    public function test_request_otp_creates_record_and_sends_mail(): void
    {
        $result = $this->service->requestOtp('user@example.com', '127.0.0.1', null);

        $this->assertTrue($result['success'] ?? false);
        $this->assertDatabaseHas('portal_otps', ['email' => 'user@example.com']);
        Mail::assertQueued(\App\Mail\OtpMail::class);
    }

    public function test_verify_otp_accepts_valid_code(): void
    {
        $code = '123456';
        PortalOtp::create([
            'email' => 'user@example.com',
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        $result = $this->service->verifyOtp('user@example.com', $code, null);

        $this->assertTrue($result['success'] ?? false);
        $this->assertSame('user@example.com', $result['email'] ?? null);
    }

    public function test_verify_otp_rejects_invalid_code(): void
    {
        PortalOtp::create([
            'email' => 'user@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        $result = $this->service->verifyOtp('user@example.com', '999999', null);

        $this->assertFalse($result['success'] ?? true);
    }
}
