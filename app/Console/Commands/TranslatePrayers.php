<?php

namespace App\Console\Commands;

use App\Models\PrayerComment;
use App\Models\PrayerMessage;
use App\Models\PrayerRequest;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TranslatePrayers extends Command
{
    /**
     * @var string
     */
    protected $signature = 'prayers:translate
        {--fresh : Re-generate translations even if some already exist}';

    /**
     * @var string
     */
    protected $description = 'Generate and persist translations for prayer requests, messages and comments';

    public function handle(): int
    {
        $models = [
            PrayerRequest::class,
            PrayerMessage::class,
            PrayerComment::class,
        ];

        foreach ($models as $model) {
            $this->translateModel($model);
        }

        $this->info('Prayer content translation completed.');

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function translateModel(string $modelClass): void
    {
        $label = class_basename($modelClass);
        $query = $modelClass::query();

        if (! $this->option('fresh')) {
            $query->where(function (Builder $builder): void {
                $builder->whereNull('translations')
                    ->orWhere('translations', '[]')
                    ->orWhere('translations', '{}');
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->line("{$label}: nothing to translate.");

            return;
        }

        $this->line("{$label}: translating {$total} record(s)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(50, function ($records) use ($bar): void {
            foreach ($records as $record) {
                if ($this->option('fresh')) {
                    $record->translations = null;
                }

                $record->generateTranslations();
                $bar->advance();

                // Be gentle with the free translation provider's rate limit.
                usleep(200_000);
            }
        });

        $bar->finish();
        $this->newLine();
    }
}
