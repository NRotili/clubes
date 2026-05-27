<?php

namespace App\Http\Controllers;

use App\Models\CuotaMensual;
use App\Models\Ingreso;
use App\Models\Pago;
use App\Models\Socio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hoy    = Carbon::today();
        $mesAct = $hoy->format('Y-m');

        // ── Socios ──────────────────────────────────────────────────
        $totalActivos     = Socio::where('estado', 'activo')->count();
        $totalInactivos   = Socio::where('estado', 'inactivo')->count();
        $totalSuspendidos = Socio::where('estado', 'suspendido')->count();
        $nuevosMes        = Socio::whereMonth('fecha_alta', $hoy->month)
                                  ->whereYear('fecha_alta', $hoy->year)
                                  ->count();

        // ── Finanzas del mes ────────────────────────────────────────
        $recaudacionMes = Pago::whereYear('fecha', $hoy->year)
            ->whereMonth('fecha', $hoy->month)
            ->sum('total');

        // ── Cuotas ──────────────────────────────────────────────────
        $cuotasImpagas = CuotaMensual::whereIn('estado', ['pendiente', 'parcial'])->get();
        $deudaTotal    = $cuotasImpagas->sum(fn($c) => $c->saldo());
        $cantImpagas   = $cuotasImpagas->count();
        $cuotasMes     = CuotaMensual::where('periodo', $mesAct)->count();

        // ── Asistencia ──────────────────────────────────────────────
        $ingresosHoy = Ingreso::whereDate('ingresado_en', $hoy)->count();
        $ingresosMes = Ingreso::whereYear('ingresado_en', $hoy->year)
                               ->whereMonth('ingresado_en', $hoy->month)
                               ->count();

        $ultimosIngresos = Ingreso::with('socio')
            ->latest('ingresado_en')
            ->take(8)
            ->get();

        // ── Socios más endeudados (top 5) ───────────────────────────
        $topDeudores = CuotaMensual::whereIn('estado', ['pendiente', 'parcial'])
            ->with('socio')
            ->get()
            ->groupBy('socio_id')
            ->map(fn($cuotas) => [
                'socio' => $cuotas->first()->socio,
                'deuda' => $cuotas->sum(fn($c) => $c->saldo()),
                'cant'  => $cuotas->count(),
            ])
            ->sortByDesc('deuda')
            ->take(5)
            ->values();

        // ── Ingresos por día (últimos 14 días) para mini-gráfico ────
        $ingresosPorDia = collect(range(13, 0))->map(function($diasAtras) {
            $fecha = Carbon::today()->subDays($diasAtras);
            return [
                'fecha' => $fecha->format('d/m'),
                'total' => Ingreso::whereDate('ingresado_en', $fecha)->count(),
            ];
        });

        return view('dashboard.index', compact(
            'totalActivos', 'totalInactivos', 'totalSuspendidos', 'nuevosMes',
            'recaudacionMes', 'deudaTotal', 'cantImpagas', 'cuotasMes',
            'ingresosHoy', 'ingresosMes', 'ultimosIngresos',
            'topDeudores', 'ingresosPorDia'
        ));
    }
}
