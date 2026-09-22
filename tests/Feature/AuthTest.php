<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_registered_via_livewire_can_login()
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Livewire User')
            ->set('email', 'livewire@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');

        $this->assertAuthenticated();

        // now logout
        auth()->logout();
        $this->assertGuest();

        // now login via volt
        Volt::test('pages.auth.login')
            ->set('form.email', 'livewire@example.com')
            ->set('form.password', 'password123')
            ->call('login')
            ->assertHasNoErrors();

        $this->assertAuthenticated();
    }
}
