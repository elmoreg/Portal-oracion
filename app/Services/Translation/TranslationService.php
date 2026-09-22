<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class TranslationService
{
    /**
     * How many on-demand translations may be fetched from the provider during a
     * single request, keeping first-render latency bounded. Reset per request
     * via {@see resetRequestBudget()} (called from the SetLocale middleware).
     */
    public const REQUEST_BUDGET = 6;

    private static int $requestCalls = 0;

    /**
     * Reset the per-request on-demand translation budget. Must be called once
     * at the start of each web request so long-running workers do not carry the
     * counter across requests.
     */
    public static function resetRequestBudget(): void
    {
        self::$requestCalls = 0;
    }

    /**
     * Reserve one on-demand translation slot for the current request. Returns
     * false once the budget is exhausted, so callers fall back to the original
     * text instead of blocking on more provider calls.
     */
    public function reserveRequestSlot(): bool
    {
        if (self::$requestCalls >= self::REQUEST_BUDGET) {
            return false;
        }

        self::$requestCalls++;

        return true;
    }

    /**
     * Translate dynamic text to the target locale.
     *
     * Returns the translated string, or null when the text could not be
     * genuinely translated (network failure, provider quota, or the provider
     * returning the source unchanged). Callers must treat null as "no
     * translation available" and fall back to the original text — this is what
     * prevents the source language from being persisted as a translation.
     */
    public function translate(string $text, ?string $targetLocale = null, string $sourceLocale = 'es'): ?string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return null;
        }

        $targetLocale = $targetLocale ?: App::getLocale();

        // Nothing to translate when the target matches the source language.
        if ($targetLocale === $sourceLocale) {
            return null;
        }

        // Prefer a curated static translation from the lang JSON files.
        $staticTranslation = __($text, [], $targetLocale);
        if (is_string($staticTranslation) && $staticTranslation !== $text) {
            return $staticTranslation;
        }

        return $this->requestFromProvider($trimmed, $sourceLocale, $targetLocale);
    }

    /**
     * Query the external translation provider (MyMemory).
     */
    private function requestFromProvider(string $text, string $sourceLocale, string $targetLocale): ?string
    {
        try {
            $response = Http::withoutVerifying()
                ->withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                ->timeout(8)
                ->get('https://api.mymemory.translated.net/get', [
                    'q' => $text,
                    'langpair' => $sourceLocale.'|'.$targetLocale,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            // MyMemory signals quota/errors through responseStatus (200 == ok).
            $status = $data['responseStatus'] ?? null;
            if ($status !== null && (int) $status !== 200) {
                return null;
            }

            $translated = $data['responseData']['translatedText'] ?? null;
            if (! is_string($translated) || $translated === '') {
                return null;
            }

            if (Str::contains($translated, 'MYMEMORY WARNING')) {
                return null;
            }

            $cleaned = trim(html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            // The provider frequently echoes the source back when it cannot
            // translate. Discard that so we never store the original language.
            if ($cleaned === '' || Str::lower($cleaned) === Str::lower($text)) {
                return null;
            }

            return $cleaned;
        } catch (Throwable) {
            return null;
        }
    }
}
