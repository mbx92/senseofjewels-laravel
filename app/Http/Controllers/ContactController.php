<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactInquiry;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(ContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $recipientEmail = Setting::query()->where('key', 'contact_email')->value('value')
            ?? config('mail.from.address');

        Mail::to($recipientEmail)->send(new ContactInquiry(
            senderName: $validated['name'],
            senderEmail: $validated['email'],
            subject: $validated['subject'],
            messageBody: $validated['message'],
        ));

        return redirect()->route('home')
            ->with('status', 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
    }
}
