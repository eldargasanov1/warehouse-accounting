<?php

namespace App\Http\Requests;

use App\Custom\Enums\OrderStatus;
use App\Rules\EnoughProductInStock;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'customer' => 'required|string|min:3|max:255',
            'warehouse_id' => 'required|integer|exists:App\Models\Warehouse,id',
            'status' => [Rule::enum(OrderStatus::class)],
            'products' => 'required|array|min:1',
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
