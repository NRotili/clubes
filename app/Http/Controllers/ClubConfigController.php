<?php

namespace App\Http\Controllers;

use App\Models\ClubConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClubConfigController extends Controller
{
    public function index(): View
    {
        return view('configuracion.club', [
            'nombre'    => ClubConfig::nombre(),
            'logo_url'  => ClubConfig::logoUrl(),
            'direccion' => ClubConfig::direccion(),
            'telefono'  => ClubConfig::telefono(),
            'email'     => ClubConfig::email(),
            'web'       => ClubConfig::web(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:150',
            'web'       => 'nullable|url|max:255',
            'logo'      => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        ClubConfig::set('club_nombre',    $data['nombre']);
        ClubConfig::set('club_direccion', $data['direccion'] ?? '');
        ClubConfig::set('club_telefono',  $data['telefono'] ?? '');
        ClubConfig::set('club_email',     $data['email'] ?? '');
        ClubConfig::set('club_web',       $data['web'] ?? '');

        if ($request->hasFile('logo')) {
            $old = ClubConfig::logoPath();
            if ($old) Storage::disk('public')->delete($old);

            $path = $request->file('logo')->store('club', 'public');
            ClubConfig::set('club_logo', $path);
        }

        if ($request->boolean('eliminar_logo')) {
            $old = ClubConfig::logoPath();
            if ($old) Storage::disk('public')->delete($old);
            ClubConfig::set('club_logo', '');
        }

        return back()->with('success', 'Datos del club actualizados correctamente.');
    }
}
