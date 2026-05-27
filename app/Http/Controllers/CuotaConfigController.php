<?php

namespace App\Http\Controllers;

use App\Models\ClubConfig;
use App\Models\CuotaConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CuotaConfigController extends Controller
{
    public function index(): View
    {
        CuotaConfig::inicializar();

        $grilla     = CuotaConfig::grilla();
        $categorias = CuotaConfig::categorias();
        $generos    = CuotaConfig::generos();

        $diaVencimiento  = ClubConfig::diaVencimiento();
        $recargoMora     = ClubConfig::recargoMora();
        $mesesSuspension = ClubConfig::mesesSuspension();

        return view('configuracion.cuotas', compact('grilla', 'categorias', 'generos', 'diaVencimiento', 'recargoMora', 'mesesSuspension'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cuotas'            => 'required|array',
            'cuotas.*.*'        => 'required|numeric|min:0',
            'dia_vencimiento'   => 'required|integer|min:1|max:28',
            'recargo_mora'      => 'required|numeric|min:0|max:100',
            'meses_suspension'  => 'required|integer|min:0|max:24',
        ]);

        foreach ($data['cuotas'] as $categoria => $porGenero) {
            foreach ($porGenero as $genero => $monto) {
                CuotaConfig::updateOrCreate(
                    ['categoria' => $categoria, 'genero' => $genero],
                    ['monto' => $monto]
                );
            }
        }

        ClubConfig::set('cuota_dia_vencimiento', $data['dia_vencimiento']);
        ClubConfig::set('cuota_recargo_mora',    $data['recargo_mora']);
        ClubConfig::set('meses_suspension',      $data['meses_suspension']);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
