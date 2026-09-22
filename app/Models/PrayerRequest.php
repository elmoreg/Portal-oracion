<?php

namespace App\Models;

use App\Enums\PrayerRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PrayerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_name',
        'email',
        'content',
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
            'status' => PrayerRequestStatus::class,
            'is_answered' => 'boolean',
            'answered_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $prayerRequest) {
            $prayerRequest->public_token ??= self::generateUniqueToken();
            $prayerRequest->status ??= PrayerRequestStatus::Pending;
        });
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
}
