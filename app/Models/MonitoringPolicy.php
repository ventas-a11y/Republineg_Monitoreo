<?php

namespace App\Models;

use Database\Factories\MonitoringPolicyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A published version of the Política de monitoreo. Versions are immutable:
 * a change is published as a new version.
 *
 * @property string $id
 * @property int $version
 * @property string $title
 * @property string $body Markdown written by an Administrador.
 * @property string|null $published_by
 * @property Carbon $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['version', 'title', 'body', 'published_by', 'published_at'])]
class MonitoringPolicy extends Model
{
    /** @use HasFactory<MonitoringPolicyFactory> */
    use HasFactory, HasUlids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * The version currently in force, or null if none was published yet.
     */
    public static function current(): ?self
    {
        return static::query()->orderByDesc('version')->first();
    }

    /**
     * Number for the next version to publish.
     */
    public static function nextVersion(): int
    {
        return (int) static::query()->max('version') + 1;
    }

    /**
     * The body as safe HTML: raw HTML in the Markdown is stripped and unsafe
     * links (javascript:, data:) are dropped.
     */
    public function bodyHtml(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * The Administrador who published this version.
     *
     * @return BelongsTo<User, $this>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Acceptances of this version.
     *
     * @return HasMany<PolicyAcceptance, $this>
     */
    public function acceptances(): HasMany
    {
        return $this->hasMany(PolicyAcceptance::class);
    }
}
