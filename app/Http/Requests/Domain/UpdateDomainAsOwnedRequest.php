<?php

namespace App\Http\Requests\Domain;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainAsOwnedRequest extends BaseRequest
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
            'acquisition_price' => 'required|string|max:255|',
            'acquisition_method' => 'required|string|max:255|',
        ];
    }



}

