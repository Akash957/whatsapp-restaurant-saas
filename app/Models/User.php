<?php

namespace App\Models;

use App\Notifications\QueuedResetPassword;
use App\Notifications\QueuedVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'restaurant_id' => 'integer',
        ];
    }

    /** @return BelongsTo<Restaurant, $this> */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /** @return BelongsTo<Role, $this> */
    public function roleDefinition(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    /** @return HasOne<RestaurantStaff, $this> */
    public function staffMembership(): HasOne
    {
        return $this->hasOne(RestaurantStaff::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canManage(string $permission): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->hasRole('super_admin', 'vendor')) {
            return true;
        }

        if (! $this->hasRole('staff') || $this->restaurant_id === null) {
            return false;
        }

        $membership = $this->staffMembership;

        if ($membership !== null
            && (int) $membership->restaurant_id === $this->restaurant_id
            && in_array($permission, $membership->permissions ?? [], true)) {
            return true;
        }

        return $this->roleDefinition?->permissions->contains('name', $permission) ?? false;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmail);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPassword($token));
    }
}
