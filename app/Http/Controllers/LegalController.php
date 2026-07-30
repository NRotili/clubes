<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    public function privacidad(): View
    {
        return view('legal.privacidad');
    }
}
