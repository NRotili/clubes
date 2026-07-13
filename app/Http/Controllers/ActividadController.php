<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Disciplina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function index(): View
    {
        $actividades = Actividad::withCount('franjas')
            ->orderBy('nombre')
            ->get();

        return view('actividades.index', compact('actividades'));
    }

    public function create(): View
    {
        $disciplinas = Disciplina::where('estado', 'activa')->orderBy('nombre')->get();
        return view('actividades.create', compact('disciplinas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->reglas());
        $data['requiere_aprobacion'] = $request->boolean('requiere_aprobacion');
        $data['requiere_pago']       = $request->boolean('requiere_pago');

        $actividad = Actividad::create($data);

        $this->sincronizarFranjas($actividad, $request->input('franjas', []));
        $actividad->disciplinasRequeridas()->sync($request->input('disciplinas_requeridas', []));

        return redirect()->route('actividades.show', $actividad)
            ->with('success', "Actividad «{$actividad->nombre}» creada exitosamente.");
    }

    public function show(Actividad $actividad): View
    {
        $actividad->load('franjas');

        return view('actividades.show', compact('actividad'));
    }

    public function edit(Actividad $actividad): View
    {
        $actividad->load('franjas', 'disciplinasRequeridas');
        $disciplinas = Disciplina::where('estado', 'activa')->orderBy('nombre')->get();
        return view('actividades.edit', compact('actividad', 'disciplinas'));
    }

    public function update(Request $request, Actividad $actividad): RedirectResponse
    {
        $data = $request->validate($this->reglas());
        $data['requiere_aprobacion'] = $request->boolean('requiere_aprobacion');
        $data['requiere_pago']       = $request->boolean('requiere_pago');

        $actividad->update($data);

        $this->sincronizarFranjas($actividad, $request->input('franjas', []));
        $actividad->disciplinasRequeridas()->sync($request->input('disciplinas_requeridas', []));

        return redirect()->route('actividades.show', $actividad)
            ->with('success', "Actividad «{$actividad->nombre}» actualizada correctamente.");
    }

    public function destroy(Actividad $actividad): RedirectResponse
    {
        $nombre = $actividad->nombre;
        $actividad->delete();

        return redirect()->route('actividades.index')
            ->with('success', "La actividad «{$nombre}» fue eliminada.");
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function reglas(): array
    {
        return [
            'nombre'              => 'required|string|max:100',
            'descripcion'         => 'nullable|string|max:500',
            'estado'              => 'required|in:activa,inactiva',
            'requiere_aprobacion' => 'nullable|boolean',
            'requiere_pago'       => 'nullable|boolean',
            'costo'               => 'nullable|required_if:requiere_pago,1|numeric|min:0',
            'anticipacion_dias'   => 'nullable|integer|min:0',
            'max_turnos_activos'  => 'nullable|integer|min:1',
        ];
    }

    private function sincronizarFranjas(Actividad $actividad, array $franjas): void
    {
        $actividad->franjas()->delete();

        foreach ($franjas as $f) {
            if (empty($f['dia_semana']) || empty($f['hora_inicio']) || empty($f['hora_fin'])
                || empty($f['duracion_minutos']) || empty($f['cupo'])) {
                continue;
            }

            $actividad->franjas()->create([
                'dia_semana'       => $f['dia_semana'],
                'hora_inicio'      => $f['hora_inicio'],
                'hora_fin'         => $f['hora_fin'],
                'duracion_minutos' => $f['duracion_minutos'],
                'cupo'             => $f['cupo'],
            ]);
        }
    }
}
