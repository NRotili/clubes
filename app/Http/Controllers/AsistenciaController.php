<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    public function index(Request $request): View
    {
        $desde    = $request->get('desde', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $hasta    = $request->get('hasta', Carbon::today()->format('Y-m-d'));
        $busqueda = $request->get('busqueda');

        $query = Ingreso::with('socio')
            ->whereDate('ingresado_en', '>=', $desde)
            ->whereDate('ingresado_en', '<=', $hasta)
            ->latest('ingresado_en');

        if ($busqueda) {
            $query->whereHas('socio', fn($q) =>
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('apellido', 'like', "%{$busqueda}%")
                  ->orWhere('numero_socio', 'like', "%{$busqueda}%")
            );
        }

        $ingresos = $query->paginate(50)->withQueryString();

        // Estadísticas del período
        $totalPeriodo = $ingresos->total();
        $diasPeriodo  = max(1, Carbon::parse($desde)->diffInDays(Carbon::parse($hasta)) + 1);
        $promedioDia  = round($totalPeriodo / $diasPeriodo, 1);

        // Ingresos por día del período (para mini barra)
        $porDia = Ingreso::selectRaw('DATE(ingresado_en) as dia, COUNT(*) as total')
            ->whereDate('ingresado_en', '>=', $desde)
            ->whereDate('ingresado_en', '<=', $hasta)
            ->when($busqueda, fn($q) =>
                $q->whereHas('socio', fn($sq) =>
                    $sq->where('nombre', 'like', "%{$busqueda}%")
                       ->orWhere('apellido', 'like', "%{$busqueda}%")
                )
            )
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        // Socio que más entró en el período
        $topSocio = Ingreso::with('socio')
            ->selectRaw('socio_id, COUNT(*) as visitas')
            ->whereDate('ingresado_en', '>=', $desde)
            ->whereDate('ingresado_en', '<=', $hasta)
            ->groupBy('socio_id')
            ->orderByDesc('visitas')
            ->first();

        return view('asistencia.index', compact(
            'ingresos', 'desde', 'hasta', 'busqueda',
            'totalPeriodo', 'promedioDia', 'porDia', 'topSocio'
        ));
    }
}
