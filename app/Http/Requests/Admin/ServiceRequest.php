<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'summary'     => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'is_active'   => ['boolean'],
        ];
    }
}
