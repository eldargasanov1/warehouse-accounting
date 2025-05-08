<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetHistoryRequest extends FormRequest
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
            'filter' => 'array|min:1',
            'filter.warehouse_id' => 'array|exists:App\Models\Warehouse,id',
            'filter.product_id' => 'array|exists:App\Models\Product,id',
            'filter.created_at' => 'date',
            'pagination' => 'array|min:2',
            'pagination.page' => 'integer|min:1',
            'pagination.perPage' => 'integer|min:1',
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
