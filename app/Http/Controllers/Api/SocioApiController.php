<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ActividadTurno;
use App\Models\AsistenciaDisciplina;
use App\Models\ClubConfig;
use App\Models\CuotaMensual;
use App\Models\Disciplina;
use App\Models\DisciplinaInscripcionLog;
use App\Models\Noticia;
use App\Services\ActividadDisponibilidadService;
use App\Services\ActividadReservaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SocioApiController extends Controller
{
    public function club(): JsonResponse
    {
        return response()->json([
            'nombre'   => ClubConfig::nombre(),
            'logo_url' => ClubConfig::logoUrl(),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user  = $request->user();
        $socio = $user->socio()->with([
            'grupoFamiliar.cuotasMensuales' => fn($q) => $q->whereIn('estado', ['pendiente', 'parcial']),
            'titular',
        ])->firstOrFail();

        $cuotasImpagas = $socio->cuotasMensuales()
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->get();
        $deudaTotal  = $cuotasImpagas->sum(fn($c) => $c->saldo());
        $cantImpagas = $cuotasImpagas->count();
        $deudaGrupo  = $deudaTotal;

        $grupoFamiliar = [];
        if ($socio->esTitular()) {
            foreach ($socio->grupoFamiliar as $m) {
                $deudaMiembro  = $m->cuotasMensuales->sum(fn($c) => $c->saldo());
                $deudaGrupo   += $deudaMiembro;
                $grupoFamiliar[] = [
                    'id'             => $m->id,
                    'numero_socio'   => $m->numero_socio,
                    'nombre'         => $m->nombre,
                    'apellido'       => $m->apellido,
                    'parentesco'     => $m->parentesco,
                    'categoria'      => $m->categoria,
                    'estado'         => $m->estado,
                    'qr_uuid'        => $m->qr_uuid,
                    'foto_url'       => $m->fotoUrl(),
                    'deuda_total'    => round((float) $deudaMiembro, 2),
                    'cuotas_impagas' => $m->cuotasMensuales->count(),
                ];
            }
        }

        return response()->json([
            'id'               => $socio->id,
            'numero_socio'     => $socio->numero_socio,
            'nombre'           => $socio->nombre,
            'apellido'         => $socio->apellido,
            'email'            => $socio->email,
            'telefono'         => $socio->telefono,
            'celular'          => $socio->celular,
            'direccion'        => $socio->direccion,
            'ciudad'           => $socio->ciudad,
            'categoria'        => $socio->categoria,
            'estado'           => $socio->estado,
            'fecha_alta'       => $socio->fecha_alta?->format('d/m/Y'),
            'qr_uuid'          => $socio->qr_uuid,
            'es_titular'       => $socio->esTitular(),
            'foto_url'         => $socio->fotoUrl(),
            'grupo_familiar'   => $grupoFamiliar,
            'deuda_total'      => round((float) $deudaTotal, 2),
            'cuotas_impagas'   => $cantImpagas,
            'deuda_grupo'      => round((float) $deudaGrupo, 2),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'telefono'  => 'nullable|string|max:20',
            'celular'   => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'ciudad'    => 'nullable|string|max:100',
        ]);

        $request->user()->socio->update($validated);

        return response()->json(['message' => 'Datos actualizados correctamente.']);
    }

    public function actualizarFoto(Request $request): JsonResponse
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $socio = $request->user()->socio;

        if ($socio->foto) {
            Storage::disk('public')->delete($socio->foto);
        }

        $socio->update([
            'foto' => $request->file('foto')->store('socios/fotos', 'public'),
        ]);

        return response()->json([
            'message'  => 'Foto actualizada correctamente.',
            'foto_url' => $socio->fotoUrl(),
        ]);
    }

    public function eliminarFoto(Request $request): JsonResponse
    {
        $socio = $request->user()->socio;

        if ($socio->foto) {
            Storage::disk('public')->delete($socio->foto);
            $socio->update(['foto' => null]);
        }

        return response()->json(['message' => 'Foto eliminada correctamente.']);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'actual'    => 'required|string',
            'nueva'     => 'required|string|min:8',
            'confirmar' => 'required|same:nueva',
        ]);

        $user = $request->user();

        if (!Hash::check($request->actual, $user->password)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta.'], 422);
        }

        $user->update(['password' => Hash::make($request->nueva)]);

        // Invalidar todas las sesiones excepto la actual
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }

    public function savePushToken(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);
        $request->user()->update(['expo_push_token' => $request->token]);
        return response()->json(['ok' => true]);
    }

    public function cuotas(Request $request): JsonResponse
    {
        $socio = $request->user()->socio;

        $mapCuota = fn($c) => [
            'id'            => $c->id,
            'periodo'       => $c->periodo,
            'periodo_label' => $c->periodoFormateado(),
            'monto_total'   => (float) $c->monto_total,
            'monto_pagado'  => (float) $c->monto_pagado,
            'saldo'         => $c->saldo(),
            'estado'        => $c->estado,
            'esta_vencida'  => $c->estaVencida(),
            'vence'         => $c->fechaVencimiento()->format('Y-m-d'),
            'recargo'       => $c->recargo(),
            'items'         => $c->items,
        ];

        $cuotas = CuotaMensual::where('socio_id', $socio->id)
            ->orderByDesc('periodo')->take(12)->get()->map($mapCuota);

        $familia = null;
        if ($socio->esTitular()) {
            $socio->loadMissing('grupoFamiliar');
            $familiarIds  = $socio->grupoFamiliar->pluck('id');
            $todasCuotas  = CuotaMensual::whereIn('socio_id', $familiarIds)
                ->orderByDesc('periodo')->get()->groupBy('socio_id');

            $familia = $socio->grupoFamiliar->map(fn($m) => [
                'id'         => $m->id,
                'nombre'     => $m->nombre,
                'apellido'   => $m->apellido,
                'parentesco' => $m->parentesco,
                'categoria'  => $m->categoria,
                'cuotas'     => ($todasCuotas[$m->id] ?? collect())->take(12)->map($mapCuota)->values(),
            ])->values();
        }

        return response()->json([
            'cuotas'           => $cuotas,
            'familia'          => $familia,
            'recargo_mora_pct' => ClubConfig::recargoMora(),
            'dia_vencimiento'  => ClubConfig::diaVencimiento(),
        ]);
    }

    public function disciplinas(Request $request): JsonResponse
    {
        $socio = $request->user()->socio()->with([
            'disciplinas' => fn($q) => $q->wherePivot('estado', 'activa')->with('horarios'),
        ])->firstOrFail();

        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        $disciplinaIds = $socio->disciplinas->pluck('id');

        // Clases dadas este mes por disciplina (fechas distintas con registros)
        $clasesDadas = DB::table('asistencia_disciplina')
            ->select('disciplina_id', DB::raw('COUNT(DISTINCT fecha) as total'))
            ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->whereIn('disciplina_id', $disciplinaIds)
            ->groupBy('disciplina_id')
            ->get()
            ->keyBy('disciplina_id');

        // Clases asistidas por este socio
        $asistidas = AsistenciaDisciplina::where('socio_id', $socio->id)
            ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->whereIn('disciplina_id', $disciplinaIds)
            ->get()
            ->groupBy('disciplina_id')
            ->map->count();

        $data = $socio->disciplinas->map(fn($d) => [
            'id'         => $d->id,
            'nombre'     => $d->nombre,
            'tipo_costo' => $d->tipo_costo,
            'costo'      => (float) $d->costo,
            'beca'       => (bool) $d->pivot->beca,
            'horarios'   => $d->horarios->map(fn($h) => [
                'dia'         => $h->dia_semana,
                'hora_inicio' => substr($h->hora_inicio, 0, 5),
                'hora_fin'    => substr($h->hora_fin, 0, 5),
            ]),
            'asistencia_mes' => [
                'clases_dadas'    => (int) ($clasesDadas[$d->id]->total ?? 0),
                'clases_asistidas' => (int) ($asistidas[$d->id] ?? 0),
            ],
        ]);

        return response()->json(['disciplinas' => $data]);
    }

    public function calendario(Request $request): JsonResponse
    {
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

        $socio = $request->user()->socio;
        $inscriptoIds = $socio->disciplinas()
            ->wherePivot('estado', 'activa')
            ->pluck('disciplinas.id')
            ->flip();

        $disciplinas = Disciplina::where('estado', 'activa')
            ->with(['horarios', 'profesores'])
            ->orderBy('nombre')
            ->get();

        $porDia = [];
        foreach ($dias as $dia) {
            $clases = [];
            foreach ($disciplinas as $d) {
                foreach ($d->horarios->where('dia_semana', $dia) as $h) {
                    $clases[] = [
                        'id'          => $d->id,
                        'nombre'      => $d->nombre,
                        'hora_inicio' => substr($h->hora_inicio, 0, 5),
                        'hora_fin'    => substr($h->hora_fin, 0, 5),
                        'profesores'  => $d->profesores->map(fn($p) => $p->nombreCompleto())->values(),
                        'inscripto'   => $inscriptoIds->has($d->id),
                    ];
                }
            }
            usort($clases, fn($a, $b) => $a['hora_inicio'] <=> $b['hora_inicio']);
            $porDia[$dia] = $clases;
        }

        return response()->json(['calendario' => $porDia]);
    }

    public function inscribirDisciplina(Request $request, Disciplina $disciplina): JsonResponse
    {
        $socio = $request->user()->socio;

        $disciplina->socios()->syncWithoutDetaching([
            $socio->id => [
                'fecha_inscripcion' => now()->toDateString(),
                'estado'            => 'activa',
            ],
        ]);

        DisciplinaInscripcionLog::create([
            'socio_id'      => $socio->id,
            'disciplina_id' => $disciplina->id,
            'accion'        => 'inscripcion',
            'origen'        => 'app',
        ]);

        return response()->json(['ok' => true]);
    }

    public function bajaDisciplina(Request $request, Disciplina $disciplina): JsonResponse
    {
        $socio = $request->user()->socio;

        $disciplina->socios()->updateExistingPivot($socio->id, ['estado' => 'baja']);

        DisciplinaInscripcionLog::create([
            'socio_id'      => $socio->id,
            'disciplina_id' => $disciplina->id,
            'accion'        => 'baja',
            'origen'        => 'app',
        ]);

        return response()->json(['ok' => true]);
    }

    public function noticias(Request $request): JsonResponse
    {
        $user     = $request->user();
        $leidasHasta = $user->noticias_leidas_hasta;

        $noticias = Noticia::with('autor')
            ->latest()
            ->get()
            ->map(fn($n) => [
                'id'     => $n->id,
                'titulo' => $n->titulo,
                'cuerpo' => $n->cuerpo,
                'fecha'  => $n->created_at->isoFormat('D [de] MMMM [de] YYYY'),
                'autor'  => $n->autor->name,
            ]);

        $noLeidas = Noticia::when(
            $leidasHasta,
            fn($q) => $q->where('created_at', '>', $leidasHasta),
        )->count();

        return response()->json(['noticias' => $noticias, 'no_leidas' => $noLeidas]);
    }

    public function marcarNoticiasLeidas(Request $request): JsonResponse
    {
        $request->user()->update(['noticias_leidas_hasta' => now()]);

        return response()->json(['ok' => true]);
    }

    public function actividades(Request $request): JsonResponse
    {
        $socio = $request->user()->socio;
        $disciplinasDelSocio = $socio
            ? $socio->disciplinas()->pluck('disciplinas.id')
            : collect();

        $actividades = Actividad::where('estado', 'activa')
            ->with('franjas', 'disciplinasRequeridas')
            ->orderBy('nombre')
            ->get()
            ->map(function (Actividad $a) use ($disciplinasDelSocio) {
                $requeridas = $a->disciplinasRequeridas;
                $puedeSolicitar = $requeridas->isEmpty()
                    || $requeridas->pluck('id')->intersect($disciplinasDelSocio)->isNotEmpty();

                return [
                    'id'                    => $a->id,
                    'nombre'                => $a->nombre,
                    'descripcion'           => $a->descripcion,
                    'requiere_aprobacion'   => $a->requiere_aprobacion,
                    'requiere_pago'         => $a->requiere_pago,
                    'costo'                 => $a->costo !== null ? (float) $a->costo : null,
                    'anticipacion_dias'     => $a->anticipacion_dias,
                    'max_turnos_activos'    => $a->max_turnos_activos,
                    'puede_solicitar'       => $puedeSolicitar,
                    'disciplinas_requeridas'=> $requeridas->map(fn ($d) => [
                        'id'     => $d->id,
                        'nombre' => $d->nombre,
                    ])->values(),
                    'franjas'               => $a->franjas->map(fn ($f) => [
                        'dia_semana'       => $f->dia_semana,
                        'hora_inicio'      => substr($f->hora_inicio, 0, 5),
                        'hora_fin'         => substr($f->hora_fin, 0, 5),
                        'duracion_minutos' => $f->duracion_minutos,
                        'cupo'             => $f->cupo,
                    ])->values(),
                ];
            });

        return response()->json(['actividades' => $actividades]);
    }

    public function disponibilidadActividad(Request $request, Actividad $actividad): JsonResponse
    {
        $request->validate(['fecha' => 'required|date']);

        $slots = ActividadDisponibilidadService::slots($actividad, Carbon::parse($request->fecha));

        return response()->json(['slots' => $slots]);
    }

    public function reservarTurno(Request $request, Actividad $actividad): JsonResponse
    {
        $request->validate([
            'fecha'         => 'required|date',
            'hora_inicio'   => 'required',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $socio = $request->user()->socio;

        try {
            $turno = ActividadReservaService::reservar(
                $actividad,
                $socio,
                $request->fecha,
                $request->hora_inicio,
                observaciones: $request->observaciones,
            );
        } catch (ValidationException $e) {
            return response()->json(['message' => collect($e->errors())->flatten()->first()], 422);
        }

        return response()->json(['turno' => $this->formatTurno($turno->load('actividad'))], 201);
    }

    public function misTurnos(Request $request): JsonResponse
    {
        $socio = $request->user()->socio;

        $turnos = $socio->turnos()
            ->with('actividad')
            ->get()
            ->map(fn (ActividadTurno $t) => $this->formatTurno($t));

        return response()->json(['turnos' => $turnos]);
    }

    public function cancelarTurno(Request $request, ActividadTurno $turno): JsonResponse
    {
        if ($turno->socio_id !== $request->user()->socio->id) {
            abort(403);
        }

        if (!$turno->puedeCancelar()) {
            return response()->json(['message' => 'Este turno no se puede cancelar.'], 422);
        }

        $turno->update(['estado' => 'cancelado']);

        return response()->json(['ok' => true]);
    }

    private function formatTurno(ActividadTurno $turno): array
    {
        return [
            'id'             => $turno->id,
            'actividad_id'   => $turno->actividad_id,
            'actividad'      => $turno->actividad->nombre,
            'fecha'          => $turno->fecha->format('Y-m-d'),
            'hora_inicio'    => substr($turno->hora_inicio, 0, 5),
            'hora_fin'       => substr($turno->hora_fin, 0, 5),
            'estado'         => $turno->estado,
            'estado_label'   => ActividadTurno::etiquetaEstado($turno->estado),
            'monto'          => $turno->monto !== null ? (float) $turno->monto : null,
            'pagado'         => $turno->pagado,
            'observaciones'  => $turno->observaciones,
            'puede_cancelar' => $turno->puedeCancelar(),
        ];
    }

    public function ingresos(Request $request): JsonResponse
    {
        $socio    = $request->user()->socio;
        $ingresos = $socio->ingresos()->take(50)->get()->map(fn($i) => [
            'id'           => $i->id,
            'ingresado_en' => $i->ingresado_en->toIso8601String(),
            'fecha'        => $i->ingresado_en->format('d/m/Y'),
            'dia_semana'   => ucfirst($i->ingresado_en->locale('es')->isoFormat('dddd')),
            'hora'         => $i->ingresado_en->format('H:i'),
        ]);

        return response()->json(['ingresos' => $ingresos]);
    }
}
