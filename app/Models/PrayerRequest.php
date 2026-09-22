<?php

namespace App\Models;

use App\Enums\PrayerRequestStatus;
use App\Jobs\TranslatePrayerContent;
use App\Models\Concerns\HasTranslatableContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PrayerRequest extends Model
{
    use HasFactory;
    use HasTranslatableContent;

    protected $fillable = [
        'requester_name',
        'email',
        'content',
        'is_public',
        'prayed_count',
        'ip_address',
        'country_code',
        'country_name',
        'status',
        'is_answered',
        'answered_at',
        'answer_note',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'prayed_count' => 'integer',
            'status' => PrayerRequestStatus::class,
            'is_answered' => 'boolean',
            'answered_at' => 'datetime',
            'translations' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $prayerRequest) {
            $prayerRequest->public_token ??= self::generateUniqueToken();
            $prayerRequest->status ??= PrayerRequestStatus::Pending;
        });

        static::created(function (self $prayerRequest) {
            TranslatePrayerContent::dispatch($prayerRequest);
        });

        static::updated(function (self $prayerRequest) {
            if ($prayerRequest->wasChanged(['content', 'answer_note'])) {
                TranslatePrayerContent::dispatch($prayerRequest);
            }
        });
    }

    /**
     * @return list<string>
     */
    public function translatableAttributes(): array
    {
        return ['content', 'answer_note'];
    }

    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(40);
        } while (self::where('public_token', $token)->exists());

        return $token;
    }

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function intercessors(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(PrayerRequestAssignment::class)
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<PrayerMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(PrayerMessage::class)->orderBy('created_at');
    }

    /**
     * @return HasMany<PrayerComment, $this>
     */
    public function publicComments(): HasMany
    {
        return $this->hasMany(PrayerComment::class)->orderBy('created_at', 'desc');
    }

    public function markAnswered(?string $note = null): void
    {
        $this->update([
            'status' => PrayerRequestStatus::Answered,
            'is_answered' => true,
            'answered_at' => now(),
            'answer_note' => $note,
        ]);
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->intercessors()->where('users.id', $user->id)->exists();
    }

    public function getTranslatedContentAttribute(): ?string
    {
        return $this->translated('content');
    }

    public function getTranslatedAnswerNoteAttribute(): ?string
    {
        return $this->translated('answer_note');
    }
}
