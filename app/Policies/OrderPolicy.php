<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->hasRole('customer')) {
            return $order->user_id !== null && (int) $order->user_id === (int) $user->getKey();
        }

        return $this->canManage($user, $order);
    }

    public function update(User $user, Order $order): bool
    {
        return $this->canManage($user, $order);
    }

    public function delete(User $user, Order $order): bool
    {
        return $this->canManage($user, $order);
    }

    private function canManage(User $user, Order $order): bool
    {
        return $user->is_active && ($user->isAdmin()
            || ($user->hasRole('vendor', 'staff') && $user->restaurant_id !== null
                && $order->restaurant_id !== null
                && $user->restaurant_id === (int) $order->restaurant_id
                && $user->canManage('manage_orders')));
    }
}
