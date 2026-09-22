<?php

namespace App\Models;

use App\Enums\MessageAuthorType;
use App\Jobs\TranslatePrayerContent;
use App\Models\Concerns\HasTranslatableContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerMessage extends Model
{
    use HasFactory;
    use HasTranslatableContent;

    protected $fillable = [
        'author_type',
        'user_id',
        'body',
    ];

    protected function casts(): array
    {
        return [
            'author_type' => MessageAuthorType::class,
            'translations' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (self $message) {
            TranslatePrayerContent::dispatch($message);
        });
    }

    /**
     * @return list<string>
     */
    public function translatableAttributes(): array
    {
        return ['body'];
    }

    public function getTranslatedBodyAttribute(): ?string
    {
        return $this->translated('body');
    }

    /**
     * @return BelongsTo<PrayerRequest, $this>
     */
    public function prayerRequest(): BelongsTo
    {
        return $this->belongsTo(PrayerRequest::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
