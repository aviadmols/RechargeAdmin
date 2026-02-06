<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\PortalOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public const OTP_EXPIRY_MINUTES = 10;

    public const MAX_ATTEMPTS = 5;

    public function requestOtp(string $email, ?string $ip = null, ?string $userAgent = null): array
    {
        $email = strtolower(trim($email));
        $code = $this->generateCode();
        $hash = Hash::make($code);

        PortalOtp::create([
            'email' => $email,
            'code_hash' => $hash,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'attempts' => 0,
            'created_ip' => $ip,
            'created_user_agent' => $userAgent,
        ]);

        Mail::to($email)->queue(new OtpMail($code));

        return ['success' => true];
    }

    public function verifyOtp(string $email, string $code, ?string $ip = null): array
    {
        $email = strtolower(trim($email));
        $code = preg_replace('/\D/', '', $code);
        if (strlen($code) !== 6) {
            return ['success' => false, 'message' => 'Invalid or expired code.'];
        }

        $otp = PortalOtp::where('email', $email)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (! $otp) {
            return ['success' => false, 'message' => 'Invalid or expired code.'];
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return ['success' => false, 'message' => 'Too many attempts. Request a new code.'];
        }

        $otp->increment('attempts');

        if (! Hash::check($code, $otp->code_hash)) {
            return ['success' => false, 'message' => 'Invalid or expired code.'];
        }

        $otp->update(['consumed_at' => now()]);

        return ['success' => true, 'email' => $email];
    }

    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
