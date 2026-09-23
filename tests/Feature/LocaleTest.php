<?php

namespace Tests\Feature;

use App\Models\PrayerComment;
use App\Models\PrayerRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_switch_locale_to_spanish(): void
    {
        $response = $this->get(route('locale.switch', 'es'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'es');

        $homeResponse = $this->withSession(['locale' => 'es'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Portal de Oración');
        $homeResponse->assertSee('Pedir Oración Ahora');
        $homeResponse->assertSee('Pido oración por la pronta recuperación');
        $homeResponse->assertSee('dir="ltr"', false);
    }

    public function test_can_switch_locale_to_english(): void
    {
        $prayer = PrayerRequest::factory()->create(['is_public' => true]);
        PrayerComment::create([
            'prayer_request_id' => $prayer->id,
            'author_name' => 'John',
            'body' => 'I am praying for you!',
        ]);

        $response = $this->get(route('locale.switch', 'en'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        $homeResponse = $this->withSession(['locale' => 'en'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Prayer Portal');
        $homeResponse->assertSee('Request Prayer Now');
        $homeResponse->assertSee('Community Interaction');
        $homeResponse->assertSee('What the community is saying');
        $homeResponse->assertSee('dir="ltr"', false);
    }

    public function test_can_switch_locale_to_portuguese(): void
    {
        $response = $this->get(route('locale.switch', 'pt'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'pt');

        $homeResponse = $this->withSession(['locale' => 'pt'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Portal de Oração');
        $homeResponse->assertSee('Pedir Oração Agora');
        $homeResponse->assertSee('Peço oração pela rápida recuperação');
        $homeResponse->assertSee('dir="ltr"', false);
    }

    public function test_can_switch_locale_to_italian(): void
    {
        $response = $this->get(route('locale.switch', 'it'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'it');

        $homeResponse = $this->withSession(['locale' => 'it'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Portale di Preghiera');
        $homeResponse->assertSee('Chiedi Preghiera Ora');
        $homeResponse->assertSee('Chiedo preghiera per la pronta guarigione');
        $homeResponse->assertSee('dir="ltr"', false);
    }

    public function test_can_switch_locale_to_arabic_with_rtl_direction(): void
    {
        $response = $this->get(route('locale.switch', 'ar'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ar');

        $homeResponse = $this->withSession(['locale' => 'ar'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('بوابة الصلاة');
        $homeResponse->assertSee('اطلب صلاة الآن');
        $homeResponse->assertSee('أطلب الصلاة من أجل الشفاء العاجل');
        $homeResponse->assertSee('dir="rtl"', false);
    }

    public function test_invalid_locale_does_not_set_invalid_session(): void
    {
        $response = $this->get(route('locale.switch', 'invalid_locale'));

        $response->assertRedirect();
        $response->assertSessionMissing('locale');

        $homeResponse = $this->withSession(['locale' => 'es'])->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Portal de Oración');
    }

    public function test_auth_pages_render_in_english_when_locale_is_en(): void
    {
        $loginResponse = $this->withSession(['locale' => 'en'])->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Dashboard Login');
        $loginResponse->assertSee('Access for intercessors and portal administrators.');
        $loginResponse->assertSee('Email Address');
        $loginResponse->assertSee('Password');
        $loginResponse->assertSee('Forgot your password?');
        $loginResponse->assertSee('Remember me');
        $loginResponse->assertSee('Sign In');
        $loginResponse->assertSee('Want to be an intercessor?');
        $loginResponse->assertSee('Sign up here');

        $registerResponse = $this->withSession(['locale' => 'en'])->get(route('register'));
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Intercessor Registration');
        $registerResponse->assertSee('Full Name');
        $registerResponse->assertSee('Confirm Password');
        $registerResponse->assertSee('Register as Intercessor');
        $registerResponse->assertSee('Already have an account?');

        $forgotResponse = $this->withSession(['locale' => 'en'])->get(route('password.request'));
        $forgotResponse->assertStatus(200);
        $forgotResponse->assertSee('Recover Password');
        $forgotResponse->assertSee('Send Recovery Link');
    }
}
