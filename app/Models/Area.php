<?php

namespace App\Models;

use Database\Factories\AreaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A group of Empleados that shares rules and app classification.
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name'])]
class Area extends Model
{
    /** @use HasFactory<AreaFactory> */
    use HasFactory, HasUlids;

    /**
     * The Empleados that belong to this Área.
     *
     * @return HasMany<User, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * The Revisores assigned to this Área.
     *
     * @return BelongsToMany<User, $this>
     */
    public function reviewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'area_reviewer')->withTimestamps();
    }
}
