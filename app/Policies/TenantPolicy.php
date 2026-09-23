<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TenantPolicy
{
    public function view(User $user, Model $model): bool
    {
        return $this->canAccess($user, $model);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->canAccess($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->canAccess($user, $model);
    }

    private function canAccess(User $user, Model $model): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->hasRole('vendor', 'staff') || $user->restaurant_id === null
            || $model->getAttribute('restaurant_id') === null
            || $user->restaurant_id !== (int) $model->getAttribute('restaurant_id')) {
            return false;
        }

        $permission = match (class_basename($model)) {
            'Product', 'Category', 'ProductVariant', 'Variant', 'Addon', 'AddOn', 'ProductAddon', 'ProductAddOn', 'ProductAddonItem', 'AddonGroup' => 'manage_catalog',
            'Order', 'OrderItem', 'OrderItemVariant', 'OrderItemAddon', 'OrderStatusHistory' => 'manage_orders',
            'Customer', 'CustomerAddress' => 'manage_customers',
            default => 'manage_settings',
        };

        return $user->canManage($permission);
    }
}
