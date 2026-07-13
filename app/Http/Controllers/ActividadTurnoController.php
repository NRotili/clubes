<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\ActividadTurno;
use App\Models\Pago;
use App\Models\PagoItem;
use App\Models\Socio;
use App\Services\ActividadDisponibilidadService;
use App\Services\ActividadReservaService;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ActividadTurnoController extends Controller
{
    public function pendientes(): View
    {
        $turnos = ActividadTurno::with(['actividad', 'socio'])
            ->where('estado', 'pendiente')
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return view('actividades.pendientes', compact('turnos'));
    }

    public function agenda(Actividad $actividad, Request $request): View
    {
        $fecha = $request->filled('fecha') ? Carbon::parse($request->fecha) : Carbon::today();
        $actividad->load('franjas');

        $slots = ActividadDisponibilidadService::slots($actividad, $fecha);

        $turnosPorHora = ActividadTurno::where('actividad_id', $actividad->id)
            ->whereDate('fecha', $fecha->format('Y-m-d'))
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->with('socio')
            ->get()
            ->groupBy(fn (ActividadTurno $t) => substr($t->hora_inicio, 0, 5));

        foreach ($slots as &$slot) {
            $slot['turnos'] = $turnosPorHora->get($slot['hora_inicio'], collect());
        }
        unset($slot);

        $sociosDisponibles = Socio::orderBy('apellido')->orderBy('nombre')->get();

        return view('actividades.agenda', compact('actividad', 'fecha', 'slots', 'sociosDisponibles'));
    }

    public function store(Request $request, Actividad $actividad): RedirectResponse
    {
        $request->validate([
            'socio_id'      => 'required|exists:socios,id',
            'fecha'         => 'required|date',
            'hora_inicio'   => 'required',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $socio = Socio::findOrFail($request->socio_id);

        try {
            $turno = ActividadReservaService::reservar(
                $actividad,
                $socio,
                $request->fecha,
                $request->hora_inicio,
                registradoPor: auth()->id(),
                observaciones: $request->observaciones,
                bypassLimites: true,
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $mensaje = "Turno creado para {$socio->nombreCompleto()}.";
        if ($turno->estado === 'pendiente') {
            $mensaje .= ' Queda pendiente de aprobación.';
        }

        return back()->with('success', $mensaje);
    }

    public function aprobar(ActividadTurno $turno): RedirectResponse
    {
        $turno->update([
            'estado'         => 'confirmado',
            'gestionado_por' => auth()->id(),
        ]);

        $turno->load(['actividad', 'socio']);

        PushNotificationService::enviarAlSocio(
            $turno->socio,
            'Turno confirmado',
            "Tu turno de {$turno->actividad->nombre} para el {$turno->fecha->format('d/m/Y')} a las " . substr($turno->hora_inicio, 0, 5) . ' fue confirmado.',
            ['tipo' => 'turno', 'turno_id' => (string) $turno->id]
        );

        return back()->with('success', 'Turno confirmado.');
    }

    public function rechazar(ActividadTurno $turno): RedirectResponse
    {
        $turno->update([
            'estado'         => 'rechazado',
            'gestionado_por' => auth()->id(),
        ]);

        $turno->load(['actividad', 'socio']);

        PushNotificationService::enviarAlSocio(
            $turno->socio,
            'Turno rechazado',
            "Tu turno de {$turno->actividad->nombre} para el {$turno->fecha->format('d/m/Y')} a las " . substr($turno->hora_inicio, 0, 5) . ' fue rechazado.',
            ['tipo' => 'turno', 'turno_id' => (string) $turno->id]
        );

        return back()->with('success', 'Turno rechazado.');
    }

    public function cancelar(ActividadTurno $turno): RedirectResponse
    {
        $turno->update(['estado' => 'cancelado']);

        return back()->with('success', 'Turno cancelado.');
    }

    public function marcarPagado(Request $request, ActividadTurno $turno): RedirectResponse
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta_debito',
        ]);

        if ($turno->pagado) {
            return back()->with('error', 'El turno ya está marcado como pagado.');
        }

        if ($turno->monto === null) {
            return back()->with('error', 'Este turno no tiene un monto asociado.');
        }

        $turno->load('actividad');
        $detalle = "Turno {$turno->actividad->nombre} {$turno->fecha->format('d/m/Y')} " . substr($turno->hora_inicio, 0, 5);

        DB::transaction(function () use ($request, $turno, $detalle) {
            $pago = Pago::create([
                'socio_id'         => $turno->socio_id,
                'cuota_mensual_id' => null,
                'fecha'            => now()->toDateString(),
                'metodo_pago'      => $request->metodo_pago,
                'total'            => $turno->monto,
                'observaciones'    => $detalle,
            ]);

            PagoItem::create([
                'pago_id'     => $pago->id,
                'descripcion' => $detalle,
                'monto'       => $turno->monto,
            ]);

            $turno->update(['pago_id' => $pago->id, 'pagado' => true]);
        });

        return back()->with('success', 'Pago registrado.');
    }
}
