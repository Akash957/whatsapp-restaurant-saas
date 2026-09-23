<?php

namespace App\Support;

use App\Models\Restaurant;

class TenantContext
{
    private ?Restaurant $currentRestaurant = null;

    public function set(Restaurant $restaurant): void
    {
        $this->currentRestaurant = $restaurant;
    }

    public function restaurant(): Restaurant
    {
        abort_if($this->currentRestaurant === null, 403, 'No restaurant context is available.');

        return $this->currentRestaurant;
    }

    public function id(): int
    {
        return (int) $this->restaurant()->getKey();
    }
}
