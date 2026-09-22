<?php

namespace App\Models;

use App\Jobs\TranslatePrayerContent;
use App\Models\Concerns\HasTranslatableContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerComment extends Model
{
    use HasFactory;
    use HasTranslatableContent;

    protected $fillable = [
        'prayer_request_id',
        'author_name',
        'body',
    ];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (self $comment) {
            TranslatePrayerContent::dispatch($comment);
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

    public function prayerRequest(): BelongsTo
    {
        return $this->belongsTo(PrayerRequest::class);
    }
}
