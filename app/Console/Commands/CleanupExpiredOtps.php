<?php

namespace App\Console\Commands;

use App\Models\PortalOtp;
use Illuminate\Console\Command;

class CleanupExpiredOtps extends Command
{
    protected $signature = 'portal:cleanup-expired-otps';

    protected $description = 'Delete consumed or expired OTP records';

    public function handle(): int
    {
        $deleted = PortalOtp::query()
            ->where(function ($q) {
                $q->whereNotNull('consumed_at')
                    ->orWhere('expires_at', '<', now());
            })
            ->delete();

        $this->info("Deleted {$deleted} OTP record(s).");
        return self::SUCCESS;
    }
}
