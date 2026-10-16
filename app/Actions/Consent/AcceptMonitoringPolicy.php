<?php

namespace App\Actions\Consent;

use App\Models\MonitoringPolicy;
use App\Models\PolicyAcceptance;
use App\Models\User;

/**
 * Records that an Empleado accepted a version of the Política de monitoreo,
 * with the request's address and browser as evidence.
 */
class AcceptMonitoringPolicy
{
    public function handle(User $user, MonitoringPolicy $policy, ?string $ipAddress, ?string $userAgent): PolicyAcceptance
    {
        return PolicyAcceptance::firstOrCreate(
            ['user_id' => $user->id, 'monitoring_policy_id' => $policy->id],
            [
                'accepted_at' => now(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent !== null ? mb_substr($userAgent, 0, 512) : null,
            ],
        );
    }
}
