<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    public function log(
        string $action,
        ?string $actorEmail,
        ?string $rechargeCustomerId,
        string $targetType,
        string $targetId,
        string $status,
        ?string $message = null,
        array $metadata = []
    ): AuditLog {
        return AuditLog::create([
            'actor_email' => $actorEmail,
            'recharge_customer_id' => $rechargeCustomerId,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'status' => $status,
            'message' => $message,
            'metadata' => $metadata,
        ]);
    }
}
