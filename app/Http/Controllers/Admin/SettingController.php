<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    private array $contactKeys = [
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_maps_embed',
        'contact_whatsapp',
    ];

    public function contact(): View
    {
        $settings = Setting::query()
            ->whereIn('key', $this->contactKeys)
            ->pluck('value', 'key');

        return view('admin.settings.contact', compact('settings'));
    }

    public function updateContact(ContactSettingRequest $request): RedirectResponse
    {
        foreach ($this->contactKeys as $key) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key, ''), 'group' => 'contact', 'type' => 'text'],
            );
        }

        return redirect()->route('admin.contact-settings')
            ->with('success', 'Informasi kontak berhasil diperbarui.');
    }
}
