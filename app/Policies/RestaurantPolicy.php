<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    public function view(User $user, Restaurant $restaurant): bool
    {
        return $this->canManage($user, $restaurant)
            || ($user->is_active && $user->hasRole('staff')
                && $user->restaurant_id !== null
                && $user->restaurant_id === (int) $restaurant->getKey());
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        return $this->canManage($user, $restaurant);
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $this->canManage($user, $restaurant);
    }

    private function canManage(User $user, Restaurant $restaurant): bool
    {
        return $user->is_active && ($user->isAdmin()
            || ($user->hasRole('vendor') && (int) $restaurant->owner_id === (int) $user->getKey()));
    }
}
