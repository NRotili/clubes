<?php

namespace App\Http\Controllers;

use App\Models\Egreso;
use App\Models\Pago;
use App\Models\Profesor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanzasController extends Controller
{
    public function index(Request $request): View
    {
        $periodo = $request->get('periodo', now()->format('Y-m'));

        [$anio, $mes] = explode('-', $periodo);

        // Ingresos: pagos del período
        $pagos = Pago::with(['socio', 'cuotaMensual'])
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->orderBy('fecha')
            ->get();

        $totalIngresos = $pagos->sum('total');

        $ingresosPorMetodo = $pagos->groupBy('metodo_pago')->map(fn($g) => [
            'total'    => $g->sum('total'),
            'cantidad' => $g->count(),
        ]);

        // Egresos: sueldos de profesores (valor actual del pivot)
        $profesoresConSueldo = Profesor::where('estado', 'activo')
            ->with('disciplinas')
            ->orderBy('apellido')
            ->get();

        $totalSueldos = $profesoresConSueldo->sum(
            fn($p) => $p->disciplinas->sum('pivot.sueldo')
        );

        // Egresos: gastos adicionales del período
        $egresos = Egreso::whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->orderBy('fecha')
            ->get();

        $totalEgresos = $totalSueldos + $egresos->sum('monto');

        $balance = $totalIngresos - $totalEgresos;

        // Períodos disponibles (para el selector)
        $periodosDisponibles = $this->periodosDisponibles();

        return view('finanzas.index', compact(
            'periodo', 'anio', 'mes',
            'pagos', 'totalIngresos', 'ingresosPorMetodo',
            'profesoresConSueldo', 'totalSueldos',
            'egresos', 'totalEgresos',
            'balance', 'periodosDisponibles'
        ));
    }

    public function storeEgreso(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'descripcion'  => 'required|string|max:255',
            'monto'        => 'required|numeric|min:0.01',
            'fecha'        => 'required|date',
            'categoria'    => 'required|in:' . implode(',', array_keys(Egreso::categorias())),
            'observaciones'=> 'nullable|string|max:500',
        ]);

        Egreso::create($data);

        $periodo = substr($data['fecha'], 0, 7);

        return redirect()->route('finanzas.index', ['periodo' => $periodo])
            ->with('success', 'Egreso registrado correctamente.');
    }

    public function destroyEgreso(Egreso $egreso): RedirectResponse
    {
        $periodo = $egreso->fecha->format('Y-m');
        $egreso->delete();

        return redirect()->route('finanzas.index', ['periodo' => $periodo])
            ->with('success', 'Egreso eliminado.');
    }

    private function periodosDisponibles(): array
    {
        $periodosPagos   = Pago::selectRaw("DATE_FORMAT(fecha, '%Y-%m') as periodo")->distinct()->pluck('periodo');
        $periodosEgresos = Egreso::selectRaw("DATE_FORMAT(fecha, '%Y-%m') as periodo")->distinct()->pluck('periodo');
        $actual = now()->format('Y-m');

        return $periodosPagos->merge($periodosEgresos)
            ->push($actual)
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();
    }
}
