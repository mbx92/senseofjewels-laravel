<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PortfolioItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['nullable', 'string', 'max:100'],
            'client_name'  => ['nullable', 'string', 'max:150'],
            'project_url'  => ['nullable', 'url', 'max:255'],
            'description'  => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'completed_at' => ['nullable', 'date'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'is_featured'  => ['boolean'],
            'is_active'    => ['boolean'],
        ];
    }
}
