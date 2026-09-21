<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    protected $signature = 'portal:make-admin {email : Email de una cuenta ya registrada}';

    protected $description = 'Convierte a una persona ya registrada en administradora del portal';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error("No existe ninguna cuenta con el email {$this->argument('email')}.");

            return self::FAILURE;
        }

        $user->update(['role' => UserRole::Admin, 'is_active' => true]);

        $this->info("{$user->name} ({$user->email}) ahora es administrador/a del portal.");

        return self::SUCCESS;
    }
}
