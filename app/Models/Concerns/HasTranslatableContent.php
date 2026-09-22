<?php

namespace App\Models\Concerns;

use App\Services\Translation\TranslationService;
use Illuminate\Support\Facades\App;

/**
 * Provides on-demand, persisted translations for user-generated text.
 *
 * Translations live in the model's `translations` JSON column, keyed by
 * attribute and then by locale, e.g.:
 *
 *     { "content": { "en": "...", "pt": "..." } }
 *
 * A translation is generated once (the first time it is requested, or via the
 * `prayers:translate` command / TranslatePrayerContent job), stored, and reused
 * on every subsequent render. When no translation is available the original
 * text is returned, so the reader always sees something.
 */
trait HasTranslatableContent
{
    /**
     * Attributes that can be translated. Override in the model if needed.
     *
     * @return list<string>
     */
    public function translatableAttributes(): array
    {
        return ['content'];
    }

    /**
     * The language the stored text is written in.
     *
     * This must be a fixed value independent of the active request locale, so
     * `fallback_locale` is used rather than `app.locale` — the latter is
     * mutated by App::setLocale() on every localized request.
     */
    public function translationSourceLocale(): string
    {
        return config('app.fallback_locale', 'es');
    }

    /**
     * Return the given attribute translated into the target (or current) locale,
     * falling back to the original text when no translation is available.
     */
    public function translated(string $attribute, ?string $locale = null): ?string
    {
        $original = $this->getAttribute($attribute);

        if (! is_string($original) || trim($original) === '') {
            return $original;
        }

        $locale ??= App::getLocale();
        $source = $this->translationSourceLocale();

        if ($locale === $source) {
            return $original;
        }

        $stored = $this->translations[$attribute][$locale] ?? null;
        if (is_string($stored) && $stored !== '') {
            return $stored;
        }

        $service = app(TranslationService::class);

        if (! $service->reserveRequestSlot()) {
            return $original;
        }

        $translated = $service->translate($original, $locale, $source);

        if ($translated === null) {
            return $original;
        }

        $this->storeTranslation($attribute, $locale, $translated);

        return $translated;
    }

    /**
     * Generate and persist translations for every target locale. Used by the
     * queued job and the backfill command; safe to re-run (skips existing).
     *
     * @param  list<string>|null  $locales
     */
    public function generateTranslations(?array $locales = null): void
    {
        $service = app(TranslationService::class);
        $source = $this->translationSourceLocale();
        $locales ??= array_keys(config('app.available_locales', []));
        $translations = $this->translations ?? [];
        $changed = false;

        foreach ($this->translatableAttributes() as $attribute) {
            $original = $this->getAttribute($attribute);
            if (! is_string($original) || trim($original) === '') {
                continue;
            }

            foreach ($locales as $locale) {
                if ($locale === $source) {
                    continue;
                }

                if (! empty($translations[$attribute][$locale])) {
                    continue;
                }

                $translated = $service->translate($original, $locale, $source);
                if ($translated !== null) {
                    $translations[$attribute][$locale] = $translated;
                    $changed = true;
                }
            }
        }

        if ($changed) {
            $this->translations = $translations;
            $this->saveQuietly();
        }
    }

    private function storeTranslation(string $attribute, string $locale, string $value): void
    {
        $translations = $this->translations ?? [];
        $translations[$attribute][$locale] = $value;
        $this->translations = $translations;
        $this->saveQuietly();
    }
}
