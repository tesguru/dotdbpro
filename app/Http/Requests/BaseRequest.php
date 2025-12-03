<?php

namespace App\Http\Requests;

use App\Traits\JsonResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    use JsonResponseTrait;

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->all();
        $firstError = $errors[0];
        throw new HttpResponseException($this->errorResponse(message: $firstError));
        //        $errors = collect($validator->errors()->messages())->mapWithKeys(
        //            function ($messages, $field) {
        //                return [$field => $messages[0]];
        //            }
        //        );
        //
        //        throw new HttpResponseException(
        //            $this->exceptionErrors(
        //                data: $errors,
        //                statusCode: 400,
        //                message: "Complete the required field"
        //            )
        //        );
    }
}
