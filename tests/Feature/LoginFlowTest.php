<?php

namespace Tests\Feature;

use App\Models\PortalOtp;
use App\Services\RechargeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_form_returns_200(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_request_otp_redirects_to_verify(): void
    {
        $response = $this->post(route('login.request'), ['email' => 'test@example.com']);
        $response->assertRedirect(route('verify'));
        $response->assertSessionHas('email', 'test@example.com');
    }

    public function test_verify_then_recharge_lookup_creates_session(): void
    {
        PortalOtp::create([
            'email' => 'customer@example.com',
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        $mock = $this->createMock(RechargeService::class);
        $mock->method('findCustomerByEmail')->willReturn(['id' => 999, 'email' => 'customer@example.com', 'first_name' => 'Test', 'last_name' => 'User']);
        $this->app->instance(RechargeService::class, $mock);

        $response = $this->post(route('verify.submit'), [
            'email' => 'customer@example.com',
            'code' => '123456',
        ]);

        $response->assertRedirect(route('account.dashboard'));
        $this->assertAuthenticated('portal');
    }

    public function test_account_requires_auth(): void
    {
        $response = $this->get(route('account.orders.index'));
        $response->assertRedirect(route('login'));
    }
}
