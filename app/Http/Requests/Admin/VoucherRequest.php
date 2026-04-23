<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'                 => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($this->route('voucher'))],
            'discount_id'          => ['required', 'exists:discounts,id'],
            'description'          => ['nullable', 'string', 'max:500'],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit'          => ['nullable', 'integer', 'min:1'],
            'per_user_limit'       => ['nullable', 'integer', 'min:1'],
            'starts_at'            => ['nullable', 'date'],
            'ends_at'              => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'            => ['boolean'],
            'image_url'            => ['nullable', 'string', 'max:500'],
        ];
    }
}
