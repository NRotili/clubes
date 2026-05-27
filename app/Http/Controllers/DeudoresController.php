<?php

namespace App\Http\Controllers;

use App\Models\CuotaMensual;
use App\Models\Socio;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeudoresController extends Controller
{
    public function index(Request $request): View
    {
        $ordenar = $request->get('ordenar', 'deuda');
        $estado  = $request->get('estado', 'activo');

        $query = Socio::whereHas('cuotasMensuales', fn($q) =>
            $q->whereIn('estado', ['pendiente', 'parcial'])
        )->with(['cuotasMensuales' => fn($q) =>
            $q->whereIn('estado', ['pendiente', 'parcial'])->orderBy('periodo')
        ]);

        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        $deudores = $query->orderBy('apellido')->get()
            ->map(function (Socio $socio) {
                $cuotas = $socio->cuotasMensuales;
                return [
                    'socio'     => $socio,
                    'deuda'     => $cuotas->sum(fn($c) => $c->saldo()),
                    'cantidad'  => $cuotas->count(),
                    'periodos'  => $cuotas->pluck('periodo'),
                    'vencidas'  => $cuotas->filter(fn($c) => $c->estaVencida())->count(),
                ];
            });

        $deudores = match ($ordenar) {
            'deuda'    => $deudores->sortByDesc('deuda'),
            'cantidad' => $deudores->sortByDesc('cantidad'),
            default    => $deudores->sortBy('socio.apellido'),
        };

        $totalDeuda    = $deudores->sum('deuda');
        $totalDeudores = $deudores->count();

        return view('deudores.index', compact(
            'deudores', 'totalDeuda', 'totalDeudores', 'ordenar', 'estado'
        ));
    }
}
