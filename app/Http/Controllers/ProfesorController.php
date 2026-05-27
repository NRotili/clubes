<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfesorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Profesor::withCount('disciplinas')->orderBy('apellido')->orderBy('nombre');

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(fn($q) => $q->where('nombre', 'like', "%{$b}%")
                ->orWhere('apellido', 'like', "%{$b}%")
                ->orWhere('cuil', 'like', "%{$b}%"));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $profesores = $query->paginate(20)->withQueryString();

        return view('profesores.index', compact('profesores'));
    }

    public function create(): View
    {
        return view('profesores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->reglas());
        $profesor = Profesor::create($data);

        return redirect()->route('profesores.show', $profesor)
            ->with('success', "Profesor {$profesor->nombreCompleto()} registrado correctamente.");
    }

    public function show(Profesor $profesor): View
    {
        $profesor->load('disciplinas');

        return view('profesores.show', compact('profesor'));
    }

    public function edit(Profesor $profesor): View
    {
        return view('profesores.edit', compact('profesor'));
    }

    public function update(Request $request, Profesor $profesor): RedirectResponse
    {
        $data = $request->validate($this->reglas($profesor->id));
        $profesor->update($data);

        return redirect()->route('profesores.show', $profesor)
            ->with('success', "Los datos de {$profesor->nombreCompleto()} fueron actualizados.");
    }

    public function destroy(Profesor $profesor): RedirectResponse
    {
        $nombre = $profesor->nombreCompleto();
        $profesor->delete();

        return redirect()->route('profesores.index')
            ->with('success', "El profesor {$nombre} fue eliminado.");
    }

    private function reglas(?int $ignorarId = null): array
    {
        return [
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'email'         => "nullable|email|max:150|unique:profesores,email,{$ignorarId}",
            'telefono'      => 'nullable|string|max:20',
            'celular'       => 'nullable|string|max:20',
            'cuil'          => 'nullable|string|max:13',
            'estado'        => 'required|in:activo,inactivo',
            'observaciones' => 'nullable|string|max:1000',
        ];
    }
}
