<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                    => ['required', 'string', 'max:150'],
            'code'                    => ['nullable', 'string', 'max:50', Rule::unique('discounts', 'code')->ignore($this->route('discount'))],
            'description'             => ['nullable', 'string', 'max:500'],
            'type'                    => ['required', 'in:fixed,percent'],
            'value'                   => ['required', 'numeric', 'min:0'],
            'applies_to'              => ['required', 'in:all,category,product'],
            'minimum_order_amount'    => ['nullable', 'numeric', 'min:0'],
            'maximum_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit'             => ['nullable', 'integer', 'min:1'],
            'starts_at'               => ['nullable', 'date'],
            'ends_at'                 => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'               => ['boolean'],
            'image_url'               => ['nullable', 'string', 'max:500'],
        ];
    }
}
