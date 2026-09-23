<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class TenantModel extends Model
{
    protected $guarded = ['id'];

    public function scopeForRestaurant(Builder $query, int $id): Builder
    {
        return $query->where($this->qualifyColumn('restaurant_id'), $id);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
