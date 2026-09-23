<?php

namespace App\Http\Requests;

class RegisterRestaurantRequest extends RegisterCustomerRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'phone' => ['required', 'string', 'min:7', 'max:30', 'regex:/^(?=.*[0-9])[0-9+().\-\s]+$/D'],
            'restaurant_name' => ['required', 'string', 'max:150'],
            'restaurant_email' => ['nullable', 'string', 'email', 'max:255'],
            'restaurant_phone' => ['nullable', 'string', 'min:7', 'max:30', 'regex:/^(?=.*[0-9])[0-9+().\-\s]+$/D'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
        ]);
    }
}
