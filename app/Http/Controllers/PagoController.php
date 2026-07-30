<?php

namespace App\Http\Controllers;

use App\Models\CuotaMensual;
use App\Models\Pago;
use App\Models\PagoItem;
use App\Models\Socio;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function create(Request $request): View
    {
        $cuota = null;
        $tieneFamiliar = false;
        if ($request->filled('cuota_id')) {
            $cuota = CuotaMensual::with(['socio.grupoFamiliar'])->findOrFail($request->cuota_id);
            $tieneFamiliar = $cuota->socio->esTitular() && $cuota->socio->grupoFamiliar->isNotEmpty();
        }

        return view('pagos.create', compact('cuota', 'tieneFamiliar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'cuota_mensual_id'  => 'nullable|exists:cuotas_mensuales,id',
            'socio_id'          => 'required|exists:socios,id',
            'fecha'             => 'required|date',
            'metodo_pago'       => 'required|in:efectivo,transferencia,tarjeta_debito',
            'observaciones'     => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.descripcion' => 'required|string|max:255',
            'items.*.monto'       => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $total = collect($request->items)->sum('monto');

            $pago = Pago::create([
                'socio_id'         => $request->socio_id,
                'cuota_mensual_id' => $request->cuota_mensual_id ?: null,
                'fecha'            => $request->fecha,
                'metodo_pago'      => $request->metodo_pago,
                'total'            => $total,
                'observaciones'    => $request->observaciones,
            ]);

            foreach ($request->items as $item) {
                PagoItem::create([
                    'pago_id'     => $pago->id,
                    'descripcion' => $item['descripcion'],
                    'monto'       => $item['monto'],
                ]);
            }

            if ($pago->cuota_mensual_id) {
                $pago->cuotaMensual->recalcularEstado();
            }
        });

        if ($request->cuota_mensual_id) {
            $cuota = CuotaMensual::with('socio')->find($request->cuota_mensual_id);
            if ($cuota?->socio) {
                $total = collect($request->items)->sum('monto');
                PushNotificationService::enviarAlSocio(
                    $cuota->socio,
                    'Pago recibido',
                    "Pago de $" . number_format($total, 2, ',', '.') . " — {$cuota->periodoFormateado()} ✓",
                    ['tipo' => 'pago', 'cuota_id' => (string) $cuota->id]
                );
            }
        }

        $destino = $request->cuota_mensual_id
            ? redirect()->route('cuotas.show', $request->cuota_mensual_id)
            : redirect()->route('cuotas.index');

        return $destino->with('success', 'Pago registrado correctamente.');
    }

    // ─── Pago grupo familiar ─────────────────────────────────────────────────

    public function createFamiliar(Request $request): View
    {
        $titular = null;
        if ($request->filled('socio_id')) {
            $titular = Socio::with('grupoFamiliar')->findOrFail($request->socio_id);
            if (!$titular->esTitular()) {
                $titular = $titular->titular()->with('grupoFamiliar')->firstOrFail();
            }
        }

        $periodo = $request->get('periodo', now()->format('Y-m'));

        // Cuotas pendientes o parciales del grupo
        $cuotasPorSocio = collect();
        if ($titular) {
            $miembros = collect([$titular])->merge($titular->grupoFamiliar);
            foreach ($miembros as $miembro) {
                $cuota = CuotaMensual::where('socio_id', $miembro->id)
                    ->where('periodo', $periodo)
                    ->whereIn('estado', ['pendiente', 'parcial'])
                    ->first();
                if ($cuota) {
                    $cuotasPorSocio->push(['socio' => $miembro, 'cuota' => $cuota]);
                }
            }
        }

        $titulares = Socio::where('estado', 'activo')
            ->whereNull('socio_titular_id')
            ->orderBy('apellido')->orderBy('nombre')
            ->get();

        return view('pagos.create-familiar', compact('titular', 'periodo', 'cuotasPorSocio', 'titulares'));
    }

    public function storeFamiliar(Request $request): RedirectResponse
    {
        $request->validate([
            'titular_id'   => 'required|exists:socios,id',
            'fecha'        => 'required|date',
            'metodo_pago'  => 'required|in:efectivo,transferencia,tarjeta_debito',
            'observaciones'=> 'nullable|string|max:500',
            'cuotas'       => 'required|array|min:1',
            'cuotas.*'     => 'exists:cuotas_mensuales,id',
        ]);

        $titular = Socio::with('grupoFamiliar')->findOrFail($request->titular_id);
        $miembrosIds = collect([$titular->id])->merge($titular->grupoFamiliar->pluck('id'));

        $cuotas = CuotaMensual::with('socio')
            ->whereIn('id', $request->cuotas)
            ->whereIn('socio_id', $miembrosIds)
            ->get();

        // Capturar saldos antes de que la transacción los actualice
        $saldos = $cuotas->mapWithKeys(fn($c) => [$c->id => $c->saldo()]);

        DB::transaction(function () use ($request, $cuotas) {
            foreach ($cuotas as $cuota) {
                $pago = Pago::create([
                    'socio_id'         => $cuota->socio_id,
                    'cuota_mensual_id' => $cuota->id,
                    'fecha'            => $request->fecha,
                    'metodo_pago'      => $request->metodo_pago,
                    'total'            => $cuota->saldo(),
                    'observaciones'    => $request->observaciones,
                ]);

                foreach ($cuota->items as $item) {
                    PagoItem::create([
                        'pago_id'     => $pago->id,
                        'descripcion' => $item['descripcion'],
                        'monto'       => $item['monto'],
                    ]);
                }

                $cuota->recalcularEstado();
            }
        });

        foreach ($cuotas as $cuota) {
            PushNotificationService::enviarAlSocio(
                $cuota->socio,
                'Pago recibido',
                "Pago de $" . number_format($saldos[$cuota->id], 2, ',', '.') . " — {$cuota->periodoFormateado()} ✓",
                ['tipo' => 'pago', 'cuota_id' => (string) $cuota->id]
            );
        }

        return redirect()->route('socios.show', $titular)
            ->with('success', "Se registraron {$cuotas->count()} pagos del grupo familiar correctamente.");
    }

    public function show(Pago $pago): View
    {
        $pago->load(['socio', 'cuotaMensual', 'items']);

        return view('pagos.show', compact('pago'));
    }

    public function destroy(Pago $pago): RedirectResponse
    {
        $cuotaId = $pago->cuota_mensual_id;

        DB::transaction(function () use ($pago) {
            $pago->items()->delete();
            $pago->delete();
        });

        if ($cuotaId) {
            $cuota = CuotaMensual::find($cuotaId);
            $cuota?->recalcularEstado();
            return redirect()->route('cuotas.show', $cuotaId)
                ->with('success', 'Pago anulado correctamente.');
        }

        return redirect()->route('cuotas.index')
            ->with('success', 'Pago anulado correctamente.');
    }
}
