<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PrayerRequestAssignment extends Pivot
{
    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }
}
