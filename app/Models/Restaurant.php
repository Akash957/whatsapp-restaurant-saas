<?php

namespace App\Models;

use Database\Factories\RestaurantFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    /** @use HasFactory<RestaurantFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'onboarding_step' => 'integer',
        ];
    }

    public function getRouteKeyName(): string { return 'slug'; }

    public function scopeForRestaurant(Builder $query, int $id): Builder
    {
        return $query->where($this->qualifyColumn('id'), $id);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(RestaurantStaff::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(RestaurantSetting::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(RestaurantDomain::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function businessHours(): HasMany
    {
        return $this->hasMany(RestaurantBusinessHour::class)->orderBy('day');
    }

    public function deliverySetting(): HasOne
    {
        return $this->hasOne(DeliverySetting::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function whatsappSetting(): HasOne
    {
        return $this->hasOne(WhatsAppSetting::class);
    }

    public function whatsappTemplates(): HasMany
    {
        return $this->hasMany(WhatsAppTemplate::class);
    }

    public function whatsappAutomations(): HasMany
    {
        return $this->hasMany(WhatsAppAutomation::class);
    }

    public function whatsappConversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
