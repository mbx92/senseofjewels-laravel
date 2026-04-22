<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InventoryAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'type'       => ['required', 'in:in,out,adjustment'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'note'       => ['required', 'string', 'max:500'],
        ];
    }
}
