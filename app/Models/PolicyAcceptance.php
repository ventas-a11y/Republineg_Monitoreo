<?php

namespace App\Models;

use Database\Factories\PolicyAcceptanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * Evidence that an Empleado accepted a version of the Política de monitoreo.
 * Insert-only: updating or deleting a record throws.
 *
 * @property string $id
 * @property string $user_id
 * @property string $monitoring_policy_id
 * @property Carbon $accepted_at
 * @property string|null $ip_address
 * @property string|null $user_agent
 */
#[Fillable(['user_id', 'monitoring_policy_id', 'accepted_at', 'ip_address', 'user_agent'])]
class PolicyAcceptance extends Model
{
    /** @use HasFactory<PolicyAcceptanceFactory> */
    use HasFactory, HasUlids;

    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * Keep acceptance records immutable from the application side too.
     */
    protected static function booted(): void
    {
        static::updating(fn (): never => throw new LogicException('Policy acceptances cannot be modified.'));
        static::deleting(fn (): never => throw new LogicException('Policy acceptances cannot be deleted.'));
    }

    /**
     * The Empleado who accepted.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The accepted version.
     *
     * @return BelongsTo<MonitoringPolicy, $this>
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(MonitoringPolicy::class, 'monitoring_policy_id');
    }
}
