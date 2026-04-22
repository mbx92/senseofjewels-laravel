<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PreferenceController extends Controller
{
    public function setLocale(Request $request): RedirectResponse
    {
        $locale = $request->input('locale', 'id');

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        session(['locale' => $locale]);
        App::setLocale($locale);

        return redirect()->back();
    }

    public function setCurrency(Request $request, CurrencyService $currency): RedirectResponse
    {
        $code = strtoupper($request->input('currency', 'IDR'));

        if (! array_key_exists($code, $currency->all())) {
            $code = 'IDR';
        }

        session(['currency' => $code]);

        return redirect()->back();
    }
}
