<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('restaurant')?->status === 'active';
    }

    public function rules(): array
    {
        if ($this->routeIs('store.cart.coupon')) {
            return ['coupon_code' => ['nullable', 'string', 'max:50']];
        }
        if ($this->routeIs('store.cart.remove')) {
            return [];
        }
        if ($this->routeIs('store.cart.update')) {
            return ['quantity' => ['required', 'integer', 'between:1,50']];
        }

        return [
            'product_id' => ['required', 'integer', 'min:1'],
            'variant_id' => ['nullable', 'integer', 'min:1'],
            'addon_ids' => ['sometimes', 'array', 'max:100'],
            'addon_ids.*' => ['required', 'integer', 'min:1', 'distinct'],
            'quantity' => ['required', 'integer', 'between:1,50'],
        ];
    }
}
