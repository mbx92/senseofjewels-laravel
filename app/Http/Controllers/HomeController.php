<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.landing');
    }
}
