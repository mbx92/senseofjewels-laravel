<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:150'],
            'position'   => ['nullable', 'string', 'max:150'],
            'company'    => ['nullable', 'string', 'max:150'],
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'message'    => ['required', 'string', 'max:1000'],
            'photo'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'is_active'  => ['boolean'],
        ];
    }
}
