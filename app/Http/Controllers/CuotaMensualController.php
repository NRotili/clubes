<?php

namespace App\Http\Controllers;

use App\Models\CuotaMensual;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CuotaMensualController extends Controller
{
    public function index(Request $request): View
    {
        $query = CuotaMensual::with('socio')
            ->orderByDesc('periodo')
            ->orderBy('estado');

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('socio', fn($q) => $q
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('apellido', 'like', "%{$buscar}%")
                ->orWhere('numero_socio', 'like', "%{$buscar}%")
            );
        }

        $cuotas   = $query->paginate(30)->withQueryString();
        $periodos = CuotaMensual::select('periodo')->distinct()->orderByDesc('periodo')->pluck('periodo');

        return view('cuotas.index', compact('cuotas', 'periodos'));
    }

    public function generar(Request $request): RedirectResponse
    {
        $request->validate([
            'periodo' => 'required|date_format:Y-m',
        ]);

        $antes     = now();
        $resultado = CuotaMensual::generarParaPeriodo($request->periodo);

        if ($resultado['creadas'] > 0) {
            CuotaMensual::where('periodo', $request->periodo)
                ->where('created_at', '>=', $antes)
                ->with('socio')
                ->get()
                ->each(fn($cuota) => PushNotificationService::enviarAlSocio(
                    $cuota->socio,
                    'Nueva cuota disponible',
                    "Tu cuota de {$cuota->periodoFormateado()} es de $" . number_format($cuota->monto_total, 2, ',', '.'),
                    ['tipo' => 'cuota_nueva', 'cuota_id' => (string) $cuota->id]
                ));
        }

        $msg = "Período {$request->periodo}: {$resultado['creadas']} cuotas generadas";
        if ($resultado['omitidas'] > 0) {
            $msg .= ", {$resultado['omitidas']} omitidas (ya existían o sin importe).";
        }

        return redirect()->route('cuotas.index', ['periodo' => $request->periodo])
            ->with('success', $msg);
    }

    public function show(CuotaMensual $cuota): View
    {
        $cuota->load(['socio', 'pagos.items']);

        return view('cuotas.show', compact('cuota'));
    }

    public function ajustarClases(Request $request, CuotaMensual $cuota): RedirectResponse
    {
        $request->validate([
            'indice' => 'required|integer|min:0',
            'clases' => 'required|integer|min:0',
        ]);

        $items = $cuota->items;
        $idx   = (int) $request->indice;

        abort_if(!isset($items[$idx]) || ($items[$idx]['tipo'] ?? '') !== 'por_clase', 422);

        $costoClase = (float) $items[$idx]['costo_clase'];
        $clases     = (int) $request->clases;

        $items[$idx]['clases'] = $clases;
        $items[$idx]['monto']  = round($costoClase * $clases, 2);
        $items[$idx]['descripcion'] = explode(' · ', $items[$idx]['descripcion'])[0]
            . ' · ' . $clases . ' ' . ($clases === 1 ? 'clase' : 'clases')
            . ' × $' . number_format($costoClase, 2, ',', '.');

        $cuota->items       = $items;
        $cuota->monto_total = collect($items)->sum('monto');
        $cuota->save();
        $cuota->recalcularEstado();

        return back()->with('success', 'Clases ajustadas correctamente.');
    }
}
