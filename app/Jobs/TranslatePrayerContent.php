<?php

namespace App\Jobs;

use App\Models\Concerns\HasTranslatableContent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Generates and persists translations for a piece of user-generated content
 * (a prayer request, chat message, or public comment) across every locale.
 *
 * @template TModel of Model
 */
class TranslatePrayerContent implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    /**
     * @param  TModel  $model
     */
    public function __construct(public Model $model) {}

    public function handle(): void
    {
        $model = $this->model->fresh();

        if ($model === null || ! in_array(HasTranslatableContent::class, class_uses_recursive($model), true)) {
            return;
        }

        /** @var Model&object{generateTranslations: callable} $model */
        $model->generateTranslations();
    }
}
