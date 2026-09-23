<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\PrayerRequestStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'country_code'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isIntercessor(): bool
    {
        return $this->role === UserRole::Intercessor;
    }

    /**
     * Scope that returns only active intercessors available for assignments.
     *
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeIntercessors(Builder $query): Builder
    {
        return $query->where('role', UserRole::Intercessor)->where('is_active', true);
    }

    /**
     * Number of prayer requests currently active (assigned or being prayed for)
     * for this intercessor. Used to balance workload during auto-assignment.
     */
    public function activeAssignmentsCount(): int
    {
        return $this->assignedPrayerRequests()
            ->whereIn('status', [
                PrayerRequestStatus::Assigned->value,
                PrayerRequestStatus::Praying->value,
            ])
            ->count();
    }

    /**
     * @return BelongsToMany<PrayerRequest, $this>
     */
    public function assignedPrayerRequests(): BelongsToMany
    {
        return $this->belongsToMany(PrayerRequest::class)
            ->using(PrayerRequestAssignment::class)
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }
}
