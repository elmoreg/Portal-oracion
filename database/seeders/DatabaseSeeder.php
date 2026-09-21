<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('portal.admin_email')],
            [
                'name' => 'Administrador del portal',
                'password' => config('portal.admin_password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );
    }
}
