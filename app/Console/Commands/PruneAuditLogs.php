<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class PruneAuditLogs extends Command
{
    protected $signature = 'audit:prune-old-logs {--days=90 : Delete logs older than this many days}';

    protected $description = 'Prune audit logs older than the specified days';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = AuditLog::query()->where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} audit log(s) older than {$days} days.");
        return self::SUCCESS;
    }
}
