<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property Role $role
 * @property string|null $area_id
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool $password_change_required
 * @property Carbon|null $temporary_password_expires_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'role', 'area_id', 'password_change_required', 'temporary_password_expires_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUlids, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_change_required' => 'boolean',
            'temporary_password_expires_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * The Área an Empleado belongs to.
     *
     * @return BelongsTo<Area, $this>
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * The Áreas a Revisor is assigned to review.
     *
     * @return BelongsToMany<Area, $this>
     */
    public function reviewedAreas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'area_reviewer')->withTimestamps();
    }

    /**
     * Versions of the Política de monitoreo this user accepted.
     *
     * @return HasMany<PolicyAcceptance, $this>
     */
    public function policyAcceptances(): HasMany
    {
        return $this->hasMany(PolicyAcceptance::class);
    }

    /**
     * Only Empleados are monitored, so only they accept the Política de monitoreo.
     */
    public function mustAcceptMonitoringPolicy(): bool
    {
        return $this->role === Role::Empleado;
    }

    /**
     * Whether the user has global access to configuration and accounts.
     */
    public function isAdministrador(): bool
    {
        return $this->role === Role::Administrador;
    }

    /**
     * Whether the user has a second factor (TOTP or passkey) to protect the panel.
     */
    public function hasSecondFactor(): bool
    {
        return $this->two_factor_confirmed_at !== null || $this->passkeys()->exists();
    }

    /**
     * Whether the temporary password from the invitation can no longer be used.
     */
    public function hasExpiredTemporaryPassword(): bool
    {
        return $this->password_change_required
            && $this->temporary_password_expires_at !== null
            && $this->temporary_password_expires_at->isPast();
    }
}
