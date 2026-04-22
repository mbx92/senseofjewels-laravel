<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContactSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_address'    => ['nullable', 'string', 'max:500'],
            'contact_phone'      => ['nullable', 'string', 'max:50'],
            'contact_email'      => ['nullable', 'email', 'max:150'],
            'contact_maps_embed' => ['nullable', 'string'],
            'contact_whatsapp'   => ['nullable', 'string', 'max:50'],
        ];
    }
}
