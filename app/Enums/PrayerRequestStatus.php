<?php

namespace App\Enums;

enum PrayerRequestStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case Praying = 'praying';
    case Answered = 'answered';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Assigned => 'Asignada',
            self::Praying => 'En oración',
            self::Answered => 'Contestada',
            self::Closed => 'Cerrada',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'bg-gray-100 text-gray-800',
            self::Assigned => 'bg-blue-100 text-blue-800',
            self::Praying => 'bg-amber-100 text-amber-800',
            self::Answered => 'bg-green-100 text-green-800',
            self::Closed => 'bg-slate-200 text-slate-600',
        };
    }
}
