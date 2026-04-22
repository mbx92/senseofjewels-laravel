<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HeroSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'subtitle'         => ['nullable', 'string', 'max:500'],
            'cta_text'         => ['nullable', 'string', 'max:80'],
            'cta_url'          => ['nullable', 'string', 'max:255'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'        => ['boolean'],
        ];
    }
}
