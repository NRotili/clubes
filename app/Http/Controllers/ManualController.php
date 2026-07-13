<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ManualController extends Controller
{
    public function index(): View
    {
        $rol = auth()->user()->rol;

        $vista = match ($rol) {
            'profesor' => 'manual.profesor',
            'socio'    => 'manual.socio',
            default    => 'manual.administracion',
        };

        return view('manual.index', ['vista' => $vista]);
    }
}
