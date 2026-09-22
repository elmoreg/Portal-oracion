<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ValidationLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_prayer_request_validation_in_spanish(): void
    {
        app()->setLocale('es');

        $component = Volt::test('pages.public.new-request')
            ->set('content', '')
            ->call('submit');

        $component->assertHasErrors(['content']);
        $errors = $component->errors()->get('content');
        $this->assertContains('El campo motivo de oración es obligatorio.', $errors);
    }

    public function test_prayer_request_validation_in_english(): void
    {
        app()->setLocale('en');

        $component = Volt::test('pages.public.new-request')
            ->set('content', '')
            ->call('submit');

        $component->assertHasErrors(['content']);
        $errors = $component->errors()->get('content');
        $this->assertContains('The prayer request field is required.', $errors);
    }

    public function test_prayer_request_validation_in_portuguese(): void
    {
        app()->setLocale('pt');

        $component = Volt::test('pages.public.new-request')
            ->set('content', '')
            ->call('submit');

        $component->assertHasErrors(['content']);
        $errors = $component->errors()->get('content');
        $this->assertContains('O campo motivo de oração é obrigatório.', $errors);
    }

    public function test_prayer_request_validation_in_italian(): void
    {
        app()->setLocale('it');

        $component = Volt::test('pages.public.new-request')
            ->set('content', '')
            ->call('submit');

        $component->assertHasErrors(['content']);
        $errors = $component->errors()->get('content');
        $this->assertContains('Il campo motivo della preghiera è obbligatorio.', $errors);
    }

    public function test_prayer_request_validation_in_arabic(): void
    {
        app()->setLocale('ar');

        $component = Volt::test('pages.public.new-request')
            ->set('content', '')
            ->call('submit');

        $component->assertHasErrors(['content']);
        $errors = $component->errors()->get('content');
        $this->assertContains('حقل طلب الصلاة مطلوب.', $errors);
    }

    public function test_registration_validation_confirmed_password_in_all_locales(): void
    {
        // Spanish
        app()->setLocale('es');
        $componentEs = Volt::test('pages.auth.register')
            ->set('name', 'Juan')
            ->set('email', 'juan@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register');
        $componentEs->assertHasErrors(['password']);
        $this->assertContains('La confirmación de contraseña no coincide.', $componentEs->errors()->get('password'));

        // Portuguese
        app()->setLocale('pt');
        $componentPt = Volt::test('pages.auth.register')
            ->set('name', 'João')
            ->set('email', 'joao@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register');
        $componentPt->assertHasErrors(['password']);
        $this->assertContains('A confirmação de senha não coincide.', $componentPt->errors()->get('password'));

        // Italian
        app()->setLocale('it');
        $componentIt = Volt::test('pages.auth.register')
            ->set('name', 'Giovanni')
            ->set('email', 'giovanni@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register');
        $componentIt->assertHasErrors(['password']);
        $this->assertContains('La conferma del campo password non coincide.', $componentIt->errors()->get('password'));

        // Arabic
        app()->setLocale('ar');
        $componentAr = Volt::test('pages.auth.register')
            ->set('name', 'أحمد')
            ->set('email', 'ahmed@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register');
        $componentAr->assertHasErrors(['password']);
        $this->assertContains('تأكيد كلمة المرور غير متطابق.', $componentAr->errors()->get('password'));
    }

    public function test_http_testimony_validation_respects_session_locale(): void
    {
        $user = User::factory()->create();

        // Spanish
        $responseEs = $this->actingAs($user)
            ->withSession(['locale' => 'es'])
            ->post(route('testimonies.store'), [
                'name' => '',
                'content' => '',
            ]);
        $responseEs->assertSessionHasErrors([
            'name' => 'El campo nombre es obligatorio.',
            'content' => 'El campo motivo de oración es obligatorio.',
        ]);

        // English
        $responseEn = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->post(route('testimonies.store'), [
                'name' => '',
                'content' => '',
            ]);
        $responseEn->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'content' => 'The prayer request field is required.',
        ]);

        // Portuguese
        $responsePt = $this->actingAs($user)
            ->withSession(['locale' => 'pt'])
            ->post(route('testimonies.store'), [
                'name' => '',
                'content' => '',
            ]);
        $responsePt->assertSessionHasErrors([
            'name' => 'O campo nome é obrigatório.',
            'content' => 'O campo motivo de oração é obrigatório.',
        ]);

        // Italian
        $responseIt = $this->actingAs($user)
            ->withSession(['locale' => 'it'])
            ->post(route('testimonies.store'), [
                'name' => '',
                'content' => '',
            ]);
        $responseIt->assertSessionHasErrors([
            'name' => 'Il campo nome è obbligatorio.',
            'content' => 'Il campo motivo della preghiera è obbligatorio.',
        ]);

        // Arabic
        $responseAr = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->post(route('testimonies.store'), [
                'name' => '',
                'content' => '',
            ]);
        $responseAr->assertSessionHasErrors([
            'name' => 'حقل الاسم مطلوب.',
            'content' => 'حقل طلب الصلاة مطلوب.',
        ]);
    }

    public function test_admin_user_form_validation_in_different_locales(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        // Spanish
        app()->setLocale('es');
        $componentEs = Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('formName', '')
            ->set('formEmail', 'not-an-email')
            ->call('saveUser');
        $componentEs->assertHasErrors(['formName', 'formEmail']);
        $this->assertContains('El campo nombre es obligatorio.', $componentEs->errors()->get('formName'));
        $this->assertContains('El campo correo electrónico debe ser una dirección de correo electrónico válida.', $componentEs->errors()->get('formEmail'));

        // English
        app()->setLocale('en');
        $componentEn = Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('formName', '')
            ->set('formEmail', 'not-an-email')
            ->call('saveUser');
        $componentEn->assertHasErrors(['formName', 'formEmail']);
        $this->assertContains('The name field is required.', $componentEn->errors()->get('formName'));
        $this->assertContains('The email field must be a valid email address.', $componentEn->errors()->get('formEmail'));
    }
}
