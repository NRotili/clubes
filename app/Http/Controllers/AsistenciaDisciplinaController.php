<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaDisciplina;
use App\Models\Disciplina;
use App\Models\Socio;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenciaDisciplinaController extends Controller
{
    private function autorizarAcceso(Disciplina $disciplina): void
    {
        $user = auth()->user();
        if ($user->esProfesor()) {
            $profesor = $user->profesor;
            abort_unless(
                $profesor && $disciplina->profesores->contains($profesor->id),
                403,
                'Solo podés acceder a las disciplinas que enseñás.'
            );
        }
    }

    public function tomar(Request $request, Disciplina $disciplina): View
    {
        $this->autorizarAcceso($disciplina);

        $fecha  = $request->get('fecha', today()->toDateString());
        $socios = $disciplina->sociosActivos()->orderBy('apellido')->orderBy('nombre')->get();

        $presentes = AsistenciaDisciplina::where('disciplina_id', $disciplina->id)
            ->where('fecha', $fecha)
            ->pluck('socio_id');

        $yaRegistrado = $presentes->isNotEmpty();

        $mapaDias = [
            'domingo' => 0, 'lunes' => 1, 'martes' => 2, 'miercoles' => 3,
            'jueves' => 4, 'viernes' => 5, 'sabado' => 6,
        ];
        $diasClase = $disciplina->horarios
            ->pluck('dia_semana')
            ->map(fn($d) => $mapaDias[$d] ?? null)
            ->filter(fn($v) => $v !== null)
            ->values();
        $esDiaDeClase = $diasClase->contains(Carbon::parse($fecha)->dayOfWeek);

        return view('disciplinas.asistencia.tomar', compact(
            'disciplina', 'socios', 'fecha', 'presentes', 'yaRegistrado', 'esDiaDeClase'
        ));
    }

    public function store(Request $request, Disciplina $disciplina): RedirectResponse
    {
        $this->autorizarAcceso($disciplina);
        $request->validate([
            'fecha'       => 'required|date',
            'presentes'   => 'nullable|array',
            'presentes.*' => 'integer|exists:socios,id',
        ]);

        $fecha     = $request->fecha;
        $presentes = collect($request->presentes ?? []);

        AsistenciaDisciplina::where('disciplina_id', $disciplina->id)
            ->where('fecha', $fecha)
            ->delete();

        foreach ($presentes as $socioId) {
            AsistenciaDisciplina::create([
                'disciplina_id'  => $disciplina->id,
                'socio_id'       => (int) $socioId,
                'fecha'          => $fecha,
                'registrado_por' => auth()->id(),
            ]);
        }

        if ($presentes->isNotEmpty()) {
            $sociosPresentes = Socio::whereIn('id', $presentes->toArray())->with('usuario')->get();
            $tokens = $sociosPresentes
                ->filter(fn($s) => $s->usuario?->expo_push_token)
                ->map(fn($s) => $s->usuario->expo_push_token)
                ->values()
                ->toArray();

            if (!empty($tokens)) {
                PushNotificationService::enviarAVarios(
                    $tokens,
                    '📋 Asistencia registrada',
                    'Tu presencia en ' . $disciplina->nombre . ' del ' . Carbon::parse($fecha)->format('d/m/Y') . ' fue registrada.',
                    ['tipo' => 'asistencia', 'disciplina_id' => $disciplina->id]
                );
            }
        }

        return redirect()
            ->route('disciplinas.asistencia.planilla', $disciplina)
            ->with('success', 'Asistencia del ' . Carbon::parse($fecha)->format('d/m/Y') . ' guardada correctamente.');
    }

    public function planilla(Request $request, Disciplina $disciplina): View
    {
        $this->autorizarAcceso($disciplina);
        $mes = $request->get('mes', today()->format('Y-m'));
        [$anio, $numMes] = explode('-', $mes);

        $inicio = Carbon::create((int) $anio, (int) $numMes, 1);
        $fin    = $inicio->copy()->endOfMonth();

        $registros = AsistenciaDisciplina::where('disciplina_id', $disciplina->id)
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->get();

        $fechas = $registros
            ->pluck('fecha')
            ->map(fn($f) => Carbon::parse($f)->toDateString())
            ->unique()->sort()->values();

        $mapa = [];
        foreach ($registros as $r) {
            $mapa[$r->socio_id][$r->fecha->toDateString()] = true;
        }

        $socios = $disciplina->sociosActivos()->orderBy('apellido')->orderBy('nombre')->get();

        $mesList = [];
        for ($i = 0; $i < 6; $i++) {
            $m = today()->subMonths($i);
            $mesList[$m->format('Y-m')] = ucfirst($m->locale('es')->isoFormat('MMMM YYYY'));
        }

        return view('disciplinas.asistencia.planilla', compact(
            'disciplina', 'fechas', 'socios', 'mapa', 'mes', 'mesList'
        ));
    }
}
