<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Intercessor = 'intercessor';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Intercessor => 'Intercesor',
        };
    }
}
