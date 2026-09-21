<?php

namespace App\Enums;

enum MessageAuthorType: string
{
    case Requester = 'requester';
    case Intercessor = 'intercessor';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Requester => 'Quien pidió oración',
            self::Intercessor => 'Intercesor',
            self::Admin => 'Equipo del portal',
        };
    }
}
