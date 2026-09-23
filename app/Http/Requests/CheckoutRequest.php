<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('restaurant')?->status === 'active';
    }

    public function rules(): array
    {
        $methods = ['cod'];
        if (config('services.razorpay.key') && config('services.razorpay.secret')) {
            $methods[] = 'razorpay';
        }

        return [
            'idempotency_key' => ['required', 'uuid'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_mobile' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'order_type' => ['required', 'in:delivery,pickup,dine_in'],
            'payment_method' => ['required', Rule::in($methods)],
            'address' => ['required_if:order_type,delivery', 'nullable', 'string', 'max:1000'],
            'building' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required_if:order_type,delivery', 'nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'tips' => ['nullable', 'regex:/^\d{1,7}(\.\d{1,2})?$/', 'numeric', 'between:0,1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => 'Choose an available payment method. Online payments must be configured before use.',
            'customer_mobile.regex' => 'Enter 7 to 15 digits, optionally starting with +.',
            'tips.regex' => 'Enter a non-negative amount with at most two decimal places.',
        ];
    }
}
