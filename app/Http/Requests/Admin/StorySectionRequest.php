<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorySectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eyebrow'         => ['nullable', 'string', 'max:100'],
            'title'           => ['nullable', 'string', 'max:80'],
            'subtitle'        => ['nullable', 'string', 'max:80'],
            'content'         => ['nullable', 'string'],
            'cta_text'        => ['nullable', 'string', 'max:100'],
            'cta_url'         => ['nullable', 'string', 'max:255'],
            'image_path'      => ['nullable', 'string', 'max:500'],
            'secondary_image' => ['nullable', 'string', 'max:500'],
            'is_active'       => ['boolean'],
        ];
    }
}
