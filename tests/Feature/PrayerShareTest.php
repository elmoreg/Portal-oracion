<?php

namespace Tests\Feature;

use App\Models\PrayerRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrayerShareTest extends TestCase
{
    use RefreshDatabase;

    public function test_share_image_is_generated_for_public_request(): void
    {
        $prayerRequest = PrayerRequest::factory()->create([
            'is_public' => true,
            'requester_name' => 'María González',
            'content' => 'Oren por la salud de mi familia.',
        ]);

        $response = $this->get(route('prayer.share-image', $prayerRequest));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertStringStartsWith("\x89PNG", $response->getContent());
    }

    public function test_share_image_supports_instagram_formats(): void
    {
        $prayerRequest = PrayerRequest::factory()->create([
            'is_public' => true,
            'content' => 'Oren por la paz.',
        ]);

        foreach (['square', 'story'] as $format) {
            $response = $this->get(route('prayer.share-image', ['prayerRequest' => $prayerRequest, 'formato' => $format]));

            $response->assertOk();
            $response->assertHeader('Content-Type', 'image/png');
            $this->assertStringStartsWith("\x89PNG", $response->getContent());
        }
    }

    public function test_share_image_can_be_downloaded_as_attachment(): void
    {
        $prayerRequest = PrayerRequest::factory()->create([
            'is_public' => true,
            'content' => 'Oren por la paz.',
        ]);

        $response = $this->get(route('prayer.share-image', [
            'prayerRequest' => $prayerRequest,
            'formato' => 'square',
            'descargar' => 1,
        ]));

        $response->assertOk();
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
    }

    public function test_share_image_is_hidden_for_private_request(): void
    {
        $prayerRequest = PrayerRequest::factory()->create([
            'is_public' => false,
        ]);

        $this->get(route('prayer.share-image', $prayerRequest))->assertNotFound();
    }

    public function test_public_pray_page_exposes_social_meta_tags_and_share_buttons(): void
    {
        $prayerRequest = PrayerRequest::factory()->create([
            'is_public' => true,
            'requester_name' => 'María González',
            'content' => 'Oren por la salud de mi familia.',
        ]);

        $response = $this->withSession(['locale' => 'es'])->get(route('prayer.pray', $prayerRequest));

        $response->assertOk();
        $response->assertSee('property="og:image"', false);
        $response->assertSee(route('prayer.share-image', $prayerRequest), false);
        $response->assertSee('name="twitter:card"', false);
        $response->assertSee('María González pide tu oración', false);
        $response->assertSee('Comparte esta petición', false);
    }
}
