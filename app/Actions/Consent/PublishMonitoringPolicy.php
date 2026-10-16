<?php

namespace App\Actions\Consent;

use App\Models\MonitoringPolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes a new version of the Política de monitoreo. Every Empleado must
 * accept it again before their data is accepted (CheckCurrentConsent).
 */
class PublishMonitoringPolicy
{
    public function handle(User $publisher, string $title, string $body): MonitoringPolicy
    {
        // The unique index on version rejects a concurrent duplicate publish.
        return DB::transaction(fn (): MonitoringPolicy => MonitoringPolicy::create([
            'version' => MonitoringPolicy::nextVersion(),
            'title' => $title,
            'body' => $body,
            'published_by' => $publisher->id,
            'published_at' => now(),
        ]));
    }
}
