<?php

namespace App\Http\Controllers;

use App\Models\Disciplina;
use App\Models\DisciplinaInscripcionLog;
use App\Models\Profesor;
use App\Models\Socio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisciplinaController extends Controller
{
    public function calendario(): View
    {
        $disciplinas = Disciplina::where('estado', 'activa')
            ->with('horarios')
            ->orderBy('nombre')
            ->get();

        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

        // Límites horarios dinámicos (redondeados a la hora)
        $minMin = 8 * 60;
        $maxMin = 20 * 60;
        foreach ($disciplinas as $d) {
            foreach ($d->horarios as $h) {
                [$hh, $mm] = explode(':', $h->hora_inicio);
                $minMin = min($minMin, (int) $hh * 60);
                [$hh, $mm] = explode(':', $h->hora_fin);
                $maxMin = max($maxMin, ((int) $hh + ($mm > 0 ? 1 : 0)) * 60);
            }
        }

        // Paleta de colores (hex) — se usan como inline styles para evitar purge de Tailwind
        $paleta = [
            '#3b82f6', '#10b981', '#8b5cf6', '#f97316',
            '#ec4899', '#14b8a6', '#f59e0b', '#6366f1',
        ];
        $colorMap = [];
        foreach ($disciplinas as $i => $d) {
            $colorMap[$d->id] = $paleta[$i % count($paleta)];
        }

        // Eventos por día
        $porDia = [];
        foreach ($dias as $dia) {
            $eventos = [];
            foreach ($disciplinas as $d) {
                foreach ($d->horarios->where('dia_semana', $dia) as $h) {
                    [$hh, $mm] = explode(':', $h->hora_inicio);
                    $inicioMin = (int) $hh * 60 + (int) $mm;
                    [$hh, $mm] = explode(':', $h->hora_fin);
                    $finMin = (int) $hh * 60 + (int) $mm;
                    $eventos[] = [
                        'disciplina' => $d,
                        'inicio_min' => $inicioMin,
                        'fin_min' => $finMin,
                        'hora_inicio' => substr($h->hora_inicio, 0, 5),
                        'hora_fin' => substr($h->hora_fin, 0, 5),
                    ];
                }
            }
            usort($eventos, fn ($a, $b) => $a['inicio_min'] <=> $b['inicio_min']);
            $porDia[$dia] = $eventos;
        }

        $horas = range($minMin / 60, $maxMin / 60);

        return view('disciplinas.calendario', compact(
            'disciplinas', 'dias', 'porDia', 'colorMap', 'minMin', 'maxMin', 'horas'
        ));
    }

    public function index(): View
    {
        $disciplinas = Disciplina::withCount('sociosActivos')
            ->with('horarios')
            ->orderBy('nombre')
            ->get();

        return view('disciplinas.index', compact('disciplinas'));
    }

    public function create(): View
    {
        return view('disciplinas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->reglas());

        $disciplina = Disciplina::create($data);

        $this->sincronizarHorarios($disciplina, $request->input('horarios', []));

        return redirect()->route('disciplinas.show', $disciplina)
            ->with('success', "Disciplina «{$disciplina->nombre}» creada exitosamente.");
    }

    public function show(Disciplina $disciplina): View
    {
        $disciplina->load([
            'horarios',
            'socios' => fn ($q) => $q->withPivot(['fecha_inscripcion', 'estado']),
            'profesores',
        ]);

        $sociosDisponibles = Socio::where('estado', 'activo')
            ->whereNotIn('id', $disciplina->socios->pluck('id'))
            ->orderBy('apellido')->orderBy('nombre')
            ->get();

        $profesoresDisponibles = Profesor::where('estado', 'activo')
            ->whereNotIn('id', $disciplina->profesores->pluck('id'))
            ->orderBy('apellido')->orderBy('nombre')
            ->get();

        return view('disciplinas.show', compact('disciplina', 'sociosDisponibles', 'profesoresDisponibles'));
    }

    public function edit(Disciplina $disciplina): View
    {
        $disciplina->load('horarios');

        return view('disciplinas.edit', compact('disciplina'));
    }

    public function update(Request $request, Disciplina $disciplina): RedirectResponse
    {
        $data = $request->validate($this->reglas());

        $disciplina->update($data);

        $this->sincronizarHorarios($disciplina, $request->input('horarios', []));

        return redirect()->route('disciplinas.show', $disciplina)
            ->with('success', "Disciplina «{$disciplina->nombre}» actualizada correctamente.");
    }

    public function destroy(Disciplina $disciplina): RedirectResponse
    {
        $nombre = $disciplina->nombre;
        $disciplina->delete();

        return redirect()->route('disciplinas.index')
            ->with('success', "La disciplina «{$nombre}» fue eliminada.");
    }

    // ─── Inscripciones ────────────────────────────────────────────────────────

    public function inscribir(Request $request, Disciplina $disciplina): RedirectResponse
    {
        $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'fecha_inscripcion' => 'required|date',
        ]);

        $disciplina->socios()->syncWithoutDetaching([
            $request->socio_id => [
                'fecha_inscripcion' => $request->fecha_inscripcion,
                'estado' => 'activa',
            ],
        ]);

        $socio = Socio::find($request->socio_id);

        DisciplinaInscripcionLog::create([
            'socio_id' => $socio->id,
            'disciplina_id' => $disciplina->id,
            'accion' => 'inscripcion',
            'origen' => 'web',
            'registrado_por' => auth()->id(),
        ]);

        return back()->with('success', "{$socio->nombreCompleto()} fue inscripto en «{$disciplina->nombre}».");
    }

    public function darBaja(Disciplina $disciplina, Socio $socio): RedirectResponse
    {
        $disciplina->socios()->updateExistingPivot($socio->id, ['estado' => 'baja']);

        DisciplinaInscripcionLog::create([
            'socio_id' => $socio->id,
            'disciplina_id' => $disciplina->id,
            'accion' => 'baja',
            'origen' => 'web',
            'registrado_por' => auth()->id(),
        ]);

        return back()->with('success', "{$socio->nombreCompleto()} fue dado de baja de «{$disciplina->nombre}».");
    }

    public function reactivar(Disciplina $disciplina, Socio $socio): RedirectResponse
    {
        $disciplina->socios()->updateExistingPivot($socio->id, ['estado' => 'activa']);

        DisciplinaInscripcionLog::create([
            'socio_id' => $socio->id,
            'disciplina_id' => $disciplina->id,
            'accion' => 'reactivacion',
            'origen' => 'web',
            'registrado_por' => auth()->id(),
        ]);

        return back()->with('success', "{$socio->nombreCompleto()} fue reactivado en «{$disciplina->nombre}».");
    }

    public function toggleBeca(Disciplina $disciplina, Socio $socio): RedirectResponse
    {
        $pivot = $disciplina->socios()->where('socio_id', $socio->id)->first()?->pivot;
        $nuevaBeca = ! ($pivot?->beca ?? false);

        $disciplina->socios()->updateExistingPivot($socio->id, ['beca' => $nuevaBeca]);

        $msg = $nuevaBeca
            ? "{$socio->nombreCompleto()} marcado con beca en «{$disciplina->nombre}»."
            : "Beca de {$socio->nombreCompleto()} en «{$disciplina->nombre}» removida.";

        return back()->with('success', $msg);
    }

    // ─── Profesores ──────────────────────────────────────────────────────────

    public function asignarProfesor(Request $request, Disciplina $disciplina): RedirectResponse
    {
        $request->validate([
            'profesor_id' => 'required|exists:profesores,id',
            'sueldo' => 'required|numeric|min:0',
        ]);

        $disciplina->profesores()->syncWithoutDetaching([
            $request->profesor_id => ['sueldo' => $request->sueldo],
        ]);

        $profesor = Profesor::find($request->profesor_id);

        return back()->with('success', "{$profesor->nombreCompleto()} fue asignado a «{$disciplina->nombre}».");
    }

    public function quitarProfesor(Disciplina $disciplina, Profesor $profesor): RedirectResponse
    {
        $disciplina->profesores()->detach($profesor->id);

        return back()->with('success', "{$profesor->nombreCompleto()} fue removido de «{$disciplina->nombre}».");
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function reglas(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'costo' => 'required|numeric|min:0',
            'tipo_costo' => 'required|in:mensual,por_clase,anual',
            'estado' => 'required|in:activa,inactiva',
        ];
    }

    private function sincronizarHorarios(Disciplina $disciplina, array $horarios): void
    {
        $disciplina->horarios()->delete();

        foreach ($horarios as $h) {
            if (empty($h['dia_semana']) || empty($h['hora_inicio']) || empty($h['hora_fin'])) {
                continue;
            }
            $disciplina->horarios()->create([
                'dia_semana' => $h['dia_semana'],
                'hora_inicio' => $h['hora_inicio'],
                'hora_fin' => $h['hora_fin'],
            ]);
        }
    }
}
