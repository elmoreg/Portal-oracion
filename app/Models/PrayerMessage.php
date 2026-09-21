<?php

namespace App\Models;

use App\Enums\MessageAuthorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_type',
        'user_id',
        'body',
    ];

    protected function casts(): array
    {
        return [
            'author_type' => MessageAuthorType::class,
        ];
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
