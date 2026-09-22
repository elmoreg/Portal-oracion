<?php

namespace Tests\Unit;

use App\Models\PrayerRequest;
use App\Services\Translation\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_translates_spanish_text_to_english_via_api(): void
    {
        Http::fake([
            'https://api.mymemory.translated.net/*' => Http::response([
                'responseStatus' => 200,
                'responseData' => [
                    'translatedText' => 'I need prayer for my pastors',
                ],
            ], 200),
        ]);

        $service = new TranslationService;
        $translated = $service->translate('necesito la oración por mis pastores', 'en', 'es');

        $this->assertSame('I need prayer for my pastors', $translated);
    }

    public function test_returns_null_when_target_matches_source(): void
    {
        $service = new TranslationService;

        $this->assertNull($service->translate('necesito oración', 'es', 'es'));
    }

    public function test_returns_null_when_provider_echoes_the_source_text(): void
    {
        // MyMemory frequently returns the source unchanged when it cannot
        // translate. This must never be persisted as a "translation".
        Http::fake([
            'https://api.mymemory.translated.net/*' => Http::response([
                'responseStatus' => 200,
                'responseData' => [
                    'translatedText' => 'necesito oración',
                ],
            ], 200),
        ]);

        $service = new TranslationService;

        $this->assertNull($service->translate('necesito oración', 'en', 'es'));
    }

    public function test_returns_null_when_provider_reports_a_quota_error(): void
    {
        Http::fake([
            'https://api.mymemory.translated.net/*' => Http::response([
                'responseStatus' => 429,
                'responseData' => [
                    'translatedText' => 'MYMEMORY WARNING: YOU USED ALL AVAILABLE FREE TRANSLATIONS',
                ],
            ], 200),
        ]);

        $service = new TranslationService;

        $this->assertNull($service->translate('necesito oración por sanidad', 'en', 'es'));
    }

    public function test_prayer_request_translated_content_accessor_persists_the_translation(): void
    {
        Http::fake([
            'https://api.mymemory.translated.net/*' => Http::response([
                'responseStatus' => 200,
                'responseData' => [
                    'translatedText' => 'I need prayer for my family',
                ],
            ], 200),
        ]);

        app()->setLocale('en');

        $prayer = PrayerRequest::factory()->create([
            'content' => 'necesito oracion por mi familia',
            'is_public' => true,
        ]);

        $this->assertSame('I need prayer for my family', $prayer->translated_content);
        $this->assertSame(
            'I need prayer for my family',
            $prayer->fresh()->translations['content']['en'] ?? null,
        );
    }

    public function test_translated_content_falls_back_to_original_when_unavailable(): void
    {
        // Provider unreachable: fake returns an empty body, so no translation.
        Http::fake([
            'https://api.mymemory.translated.net/*' => Http::response([], 200),
        ]);

        app()->setLocale('en');

        $prayer = PrayerRequest::factory()->create([
            'content' => 'necesito oracion por mi familia',
        ]);

        $this->assertSame('necesito oracion por mi familia', $prayer->translated_content);
        $this->assertNull($prayer->fresh()->translations['content']['en'] ?? null);
    }
}
