<?php

namespace App\Http\Requests\Domain;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSoldDomainRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
  public function rules(): array
    {
        return [
            'domain_id' => 'required|string|max:255|exists:domains,domain_id',
            'revenue' => 'required',
            'sold_price' => 'required',
            'date_sold' => 'required',
            'lander_sold' => 'required',
            'sale_note' => 'required',
            'sale_mode' => 'required',
        ];
    }

}

