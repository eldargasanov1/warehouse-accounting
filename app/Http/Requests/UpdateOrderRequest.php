<?php

namespace App\Http\Requests;

use App\Rules\EnoughProductInStock;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer' => 'string|min:3|max:255',
            'products' => 'array|min:1',
            'products.*' => [new EnoughProductInStock],
            'products.*.id' => 'required|integer',
            'products.*.count' => 'required|integer|min:1',
        ];
    }

    /**
     * Determine what to do if validation failed.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
