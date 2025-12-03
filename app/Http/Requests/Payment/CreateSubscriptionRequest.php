<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateSubscriptionRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust if you want to restrict access
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|string|',
            'quantity' => 'required|integer|min:1',
            'customer_email' => 'required|email|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_country' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_street' => 'required|string|max:255',
            'billing_zipcode' => 'required|string|max:20',
            'return_url' => 'required|url',
            'payment_link' => 'boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'payment_link' => $this->has('payment_link') ? (bool) $this->payment_link : true,
        ]);
    }
}
