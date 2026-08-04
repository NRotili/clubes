<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use App\Models\Egreso;
use App\Models\Pago;
use App\Models\Profesor;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        $ingresosPorMetodo = $pagos->groupBy('metodo_pago')->map(fn ($g) => [
            'total' => $g->sum('total'),
            'cantidad' => $g->count(),
        ]);

        // Sueldos de profesores (valor configurado en el pivot, informativo)
        $profesoresConSueldo = Profesor::where('estado', 'activo')
            ->with('disciplinas')
            ->orderBy('apellido')
            ->get();

        $totalSueldos = $profesoresConSueldo->sum(
            fn ($p) => $p->disciplinas->sum('pivot.sueldo')
        );

        // Egresos del período (incluye sueldos ya pagados, cargados como Egreso real)
        $egresos = Egreso::whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->orderBy('fecha')
            ->get();

        $sueldosPagados = $egresos->where('categoria', 'sueldos')
            ->whereNotNull('profesor_id')
            ->keyBy('profesor_id');

        $totalEgresos = $egresos->sum('monto');

        $balance = $totalIngresos - $totalEgresos;

        // Saldo de caja acumulado desde la apertura vigente
        $apertura = CajaApertura::vigente();
        $saldoActual = $this->saldoAcumulado($apertura);

        // Períodos disponibles (para el selector)
        $periodosDisponibles = $this->periodosDisponibles();

        return view('finanzas.index', compact(
            'periodo', 'anio', 'mes',
            'pagos', 'totalIngresos', 'ingresosPorMetodo',
            'profesoresConSueldo', 'totalSueldos', 'sueldosPagados',
            'egresos', 'totalEgresos',
            'balance', 'apertura', 'saldoActual',
            'periodosDisponibles'
        ));
    }

    public function storeApertura(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string|max:255',
        ]);

        CajaApertura::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('finanzas.index', ['periodo' => $request->get('periodo')])
            ->with('success', 'Saldo inicial guardado correctamente.');
    }

    public function pagarSueldo(Profesor $profesor, Request $request): RedirectResponse
    {
        $periodo = $request->validate([
            'periodo' => 'required|date_format:Y-m',
        ])['periodo'];

        [$anio, $mes] = explode('-', $periodo);

        $yaPagado = Egreso::where('profesor_id', $profesor->id)
            ->where('categoria', 'sueldos')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->exists();

        if ($yaPagado) {
            return redirect()->route('finanzas.index', ['periodo' => $periodo])
                ->with('error', 'Ya se registró el pago del sueldo de este profesor para el período.');
        }

        $monto = $profesor->disciplinas->sum('pivot.sueldo');

        if ($monto <= 0) {
            return redirect()->route('finanzas.index', ['periodo' => $periodo])
                ->with('error', 'Este profesor no tiene sueldo configurado.');
        }

        Egreso::create([
            'descripcion' => "Sueldo — {$profesor->nombreCompleto()} — {$periodo}",
            'monto' => $monto,
            'fecha' => "{$anio}-{$mes}-01",
            'categoria' => 'sueldos',
            'profesor_id' => $profesor->id,
        ]);

        return redirect()->route('finanzas.index', ['periodo' => $periodo])
            ->with('success', "Sueldo de {$profesor->nombreCompleto()} registrado como pagado.");
    }

    public function storeEgreso(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'categoria' => 'required|in:'.implode(',', array_keys(Egreso::categorias())),
            'observaciones' => 'nullable|string|max:500',
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

    public function exportarPdf(Request $request): Response
    {
        $data = $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $pagos = Pago::with(['socio', 'cuotaMensual'])
            ->whereBetween('fecha', [$data['desde'], $data['hasta']])
            ->orderBy('fecha')
            ->get();

        $egresos = Egreso::whereBetween('fecha', [$data['desde'], $data['hasta']])
            ->orderBy('fecha')
            ->get();

        $totalIngresos = $pagos->sum('total');
        $totalEgresos = $egresos->sum('monto');

        $apertura = CajaApertura::vigente();
        $saldoInicio = $this->saldoAcumulado($apertura, $data['desde']);
        $saldoFin = $saldoInicio + $totalIngresos - $totalEgresos;

        $html = view('finanzas.pdf', [
            'desde' => $data['desde'],
            'hasta' => $data['hasta'],
            'pagos' => $pagos,
            'egresos' => $egresos,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'saldoInicio' => $saldoInicio,
            'saldoFin' => $saldoFin,
        ])->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"movimientos_{$data['desde']}_a_{$data['hasta']}.pdf\"",
        ]);
    }

    /** Saldo de caja acumulado desde la apertura vigente. Si $antesDe se indica, calcula el saldo hasta el día anterior a esa fecha. */
    private function saldoAcumulado(?CajaApertura $apertura, ?string $antesDe = null): float
    {
        $totalIngresos = Pago::when($apertura, fn ($q) => $q->whereDate('fecha', '>=', $apertura->fecha))
            ->when($antesDe, fn ($q) => $q->whereDate('fecha', '<', $antesDe))
            ->sum('total');

        $totalEgresos = Egreso::when($apertura, fn ($q) => $q->whereDate('fecha', '>=', $apertura->fecha))
            ->when($antesDe, fn ($q) => $q->whereDate('fecha', '<', $antesDe))
            ->sum('monto');

        return ($apertura->monto ?? 0) + $totalIngresos - $totalEgresos;
    }

    private function periodosDisponibles(): array
    {
        $periodosPagos = Pago::selectRaw("DATE_FORMAT(fecha, '%Y-%m') as periodo")->distinct()->pluck('periodo');
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
