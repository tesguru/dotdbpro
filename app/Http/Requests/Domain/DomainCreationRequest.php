<?php

namespace App\Http\Requests\Domain;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DomainCreationRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domains' => 'required|array|min:1',
            'domains.*.domain' => [
                'required',
                'string',
                'regex:/^(?!www\.)[a-zA-Z0-9-]{1,63}\.[a-zA-Z]{2,}$/'
            ],
            'domains.*.acquisition_price' => 'nullable|numeric|min:0',
            'domains.*.acquisition_method' => 'nullable|string|in:Purchase,Auction,Drop Catch,Private Sale,Transfer,Gift,Other',
        ];
    }

    public function messages(): array
    {
        return [
            'domains.required' => 'Please provide at least one domain.',
            'domains.array' => 'Domains must be provided as an array.',
            'domains.*.domain.required' => 'Each domain is required.',
            'domains.*.domain.regex' => 'Each domain must be valid and not start with www. or contain subdomains.',
            'domains.*.acquisition_price.numeric' => 'Acquisition price must be a valid number.',
            'domains.*.acquisition_method.in' => 'Invalid acquisition method.',
        ];
    }
}
