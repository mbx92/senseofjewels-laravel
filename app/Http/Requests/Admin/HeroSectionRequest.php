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
            // Panel 1 — Campaign Hero
            'title'                => ['nullable', 'string', 'max:40'],
            'subtitle'             => ['nullable', 'string', 'max:40'],
            'description'          => ['nullable', 'string', 'max:500'],
            'season_badge'         => ['nullable', 'string', 'max:80'],
            'eyebrow'              => ['nullable', 'string', 'max:80'],
            'cta_text'             => ['nullable', 'string', 'max:80'],
            'cta_url'              => ['nullable', 'string', 'max:255'],
            'hero_slides'          => ['nullable', 'string'],
            'background_image_url' => ['nullable', 'string', 'max:500'],
            // Panel 2 — Product Banner
            'banner1_label'        => ['nullable', 'string', 'max:80'],
            'banner1_title'        => ['nullable', 'string', 'max:255'],
            'banner1_subtitle'     => ['nullable', 'string', 'max:255'],
            'banner1_cta_text'     => ['nullable', 'string', 'max:80'],
            'banner1_cta_url'      => ['nullable', 'string', 'max:255'],
            'banner1_image'        => ['nullable', 'string', 'max:500'],
            // Panel 3 — Category Banner
            'banner2_label'        => ['nullable', 'string', 'max:80'],
            'banner2_title'        => ['nullable', 'string', 'max:255'],
            'banner2_subtitle'     => ['nullable', 'string', 'max:255'],
            'banner2_cta_text'     => ['nullable', 'string', 'max:80'],
            'banner2_cta_url'      => ['nullable', 'string', 'max:255'],
            'banner2_image'        => ['nullable', 'string', 'max:500'],
            'text_position'         => ['nullable', 'string', 'in:top-left,top-center,top-right,middle-left,middle-center,middle-right,bottom-left,bottom-center,bottom-right'],
            'banner1_text_position' => ['nullable', 'string', 'in:top-left,top-center,top-right,middle-left,middle-center,middle-right,bottom-left,bottom-center,bottom-right'],
            'banner2_text_position' => ['nullable', 'string', 'in:top-left,top-center,top-right,middle-left,middle-center,middle-right,bottom-left,bottom-center,bottom-right'],
            'is_active'            => ['boolean'],
        ];
    }
}
