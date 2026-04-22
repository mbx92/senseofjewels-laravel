<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'sku'               => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->route('product'))],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'price'             => ['required', 'numeric', 'min:0'],
            'compare_at_price'  => ['nullable', 'numeric', 'min:0'],
            'cost_price'        => ['nullable', 'numeric', 'min:0'],
            'stock'             => ['required', 'integer', 'min:0'],
            'min_stock_alert'   => ['nullable', 'integer', 'min:0'],
            'weight'            => ['nullable', 'numeric', 'min:0'],
            'is_featured'       => ['boolean'],
            'is_active'         => ['boolean'],
            'images'            => ['nullable', 'array', 'max:8'],
            'images.*'          => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
