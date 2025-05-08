<?php

namespace App\Http\Requests;

use App\Custom\Enums\OrderStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetOrdersRequest extends FormRequest
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
            'filter.id' => 'array|exists:App\Models\Order,id',
            'filter.customer' => 'string|min:3|max:255',
            'filter.warehouse_id' => 'array|exists:App\Models\Warehouse,id',
            'filter.status' => 'string|in:' . implode(',', OrderStatus::values()),
            'filter.completed_at' => 'nullable|date',
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
