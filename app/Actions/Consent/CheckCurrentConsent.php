<?php

namespace App\Actions\Consent;

use App\Models\MonitoringPolicy;
use App\Models\User;
use Throwable;

/**
 * Answers "may monitoring data of this Empleado be accepted?" It fails
 * closed: no published policy, no acceptance of the current version, a
 * non-Empleado or any error all answer false (docs/SEGURIDAD.md, Q43).
 * The ingestion API (ticket 06) relies on it.
 */
class CheckCurrentConsent
{
    public function handle(User $user): bool
    {
        try {
            if (! $user->mustAcceptMonitoringPolicy()) {
                return false;
            }

            $current = MonitoringPolicy::current();

            return $current !== null
                && $user->policyAcceptances()->where('monitoring_policy_id', $current->id)->exists();
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
