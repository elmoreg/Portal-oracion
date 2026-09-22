<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin,
        ]);
    }

    public function test_guests_cannot_access_users_page(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_intercessors_cannot_access_users_page(): void
    {
        $intercessor = User::factory()->create();

        $this->actingAs($intercessor)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admins_can_access_users_page(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_users_are_listed_with_pagination(): void
    {
        $admin = $this->createAdmin();
        User::factory()->count(5)->create();

        $component = Volt::actingAs($admin)
            ->test('pages.admin.users.index');

        $component->assertSee($admin->name);
    }

    public function test_search_filters_users_by_name(): void
    {
        $admin = $this->createAdmin();
        $targetUser = User::factory()->create(['name' => 'María Especial']);
        User::factory()->create(['name' => 'Pedro Otro']);

        $component = Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('search', 'María');

        $component->assertSee('María Especial')
            ->assertDontSee('Pedro Otro');
    }

    public function test_search_filters_users_by_email(): void
    {
        $admin = $this->createAdmin();
        User::factory()->create(['email' => 'buscado@example.com', 'name' => 'Usuario Buscado']);
        User::factory()->create(['email' => 'otro@example.com', 'name' => 'Usuario Otro']);

        $component = Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('search', 'buscado@');

        $component->assertSee('Usuario Buscado')
            ->assertDontSee('Usuario Otro');
    }

    public function test_filter_by_role(): void
    {
        $admin = $this->createAdmin();
        $intercessor = User::factory()->create(['name' => 'Intercesor Uno']);

        $component = Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('roleFilter', 'intercessor');

        $component->assertSee('Intercesor Uno')
            ->assertDontSee($admin->name);
    }

    public function test_filter_by_status(): void
    {
        $admin = $this->createAdmin();
        $activeUser = User::factory()->create(['name' => 'Activo User', 'is_active' => true]);
        $inactiveUser = User::factory()->create(['name' => 'Inactivo User', 'is_active' => false]);

        $component = Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('statusFilter', '0');

        $component->assertSee('Inactivo User')
            ->assertDontSee('Activo User');
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->createAdmin();

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('formName', 'Nuevo Usuario')
            ->set('formEmail', 'nuevo@portal.test')
            ->set('formPassword', 'password123')
            ->set('formPassword_confirmation', 'password123')
            ->set('formRole', 'intercessor')
            ->call('saveUser');

        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@portal.test',
            'role' => 'intercessor',
        ]);
    }

    public function test_create_user_validates_required_fields(): void
    {
        $admin = $this->createAdmin();

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('formName', '')
            ->set('formEmail', '')
            ->set('formPassword', '')
            ->call('saveUser')
            ->assertHasErrors(['formName', 'formEmail', 'formPassword']);
    }

    public function test_create_user_validates_unique_email(): void
    {
        $admin = $this->createAdmin();
        User::factory()->create(['email' => 'existente@portal.test']);

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('formName', 'Duplicado')
            ->set('formEmail', 'existente@portal.test')
            ->set('formPassword', 'password123')
            ->set('formPassword_confirmation', 'password123')
            ->call('saveUser')
            ->assertHasErrors(['formEmail']);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['name' => 'Nombre Original']);

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->call('openEditModal', $user->id)
            ->set('formName', 'Nombre Editado')
            ->set('formEmail', $user->email)
            ->set('formRole', 'admin')
            ->call('saveUser');

        $user->refresh();
        $this->assertEquals('Nombre Editado', $user->name);
        $this->assertEquals(UserRole::Admin, $user->role);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['is_active' => true]);

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->call('toggleActive', $user->id);

        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = $this->createAdmin();

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->call('toggleActive', $admin->id)
            ->assertForbidden();
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create();

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->call('confirmDelete', $user->id)
            ->call('deleteUser');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->createAdmin();

        Volt::actingAs($admin)
            ->test('pages.admin.users.index')
            ->set('deletingUserId', $admin->id)
            ->call('deleteUser')
            ->assertForbidden();
    }
}
