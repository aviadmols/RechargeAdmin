<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PortalCustomer;
use App\Services\AuditLogService;
use App\Services\OtpService;
use App\Services\RechargeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected RechargeService $recharge,
        protected AuditLogService $audit
    ) {}

    public function showLoginForm(): View
    {
        return view('login');
    }

    public function loginWithPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (auth()->guard('portal')->attempt(
            $request->only('email', 'password'),
            (bool) $request->boolean('remember')
        )) {
            $user = auth()->guard('portal')->user();
            $this->audit->log(
                'portal.login',
                $user->email,
                $user->recharge_customer_id ?? null,
                'session',
                'password',
                'success',
                null,
                []
            );
            $request->session()->regenerate();
            return redirect()->intended(route('account.dashboard'));
        }

        return back()->withErrors(['email' => __('auth.failed')])->withInput($request->only('email'));
    }

    public function requestOtp(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $this->otpService->requestOtp(
            $request->input('email'),
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('verify')->with('email', $request->input('email'));
    }

    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $email = session('email') ?? $request->query('email');
        if (! $email) {
            return redirect()->route('login');
        }
        return view('verify', ['email' => $email]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $result = $this->otpService->verifyOtp(
            $request->input('email'),
            $request->input('code'),
            $request->ip()
        );

        if (! ($result['success'] ?? false)) {
            return back()->withErrors(['code' => $result['message'] ?? 'Invalid or expired code.'])->withInput();
        }

        $email = $result['email'];
        $customer = $this->recharge->findCustomerByEmail($email);

        if (! $customer) {
            return back()->withErrors(['code' => 'No account found for this email.'])->withInput();
        }

        $rechargeCustomerId = (string) ($customer['id'] ?? $customer['customer']['id'] ?? '');
        if ($rechargeCustomerId === '') {
            return back()->withErrors(['code' => 'Unable to sign in.'])->withInput();
        }

        $portalCustomer = PortalCustomer::updateOrCreate(
            ['email' => $email],
            [
                'recharge_customer_id' => $rechargeCustomerId,
                'first_name' => $customer['first_name'] ?? null,
                'last_name' => $customer['last_name'] ?? null,
                'last_synced_at' => now(),
            ]
        );

        auth()->guard('portal')->login($portalCustomer, true);

        $this->audit->log(
            'portal.login_otp',
            $portalCustomer->email,
            $portalCustomer->recharge_customer_id ?? null,
            'session',
            'otp',
            'success',
            null,
            []
        );

        return redirect()->intended(route('account.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->guard('portal')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
