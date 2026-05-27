<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::with('socio')->orderBy('name')->paginate(20);

        return view('usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        $socios = Socio::whereDoesntHave('usuario')->orderBy('apellido')->orderBy('nombre')->get();

        return view('usuarios.create', compact('socios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'rol'      => 'required|in:desarrollador,administracion,socio',
            'socio_id' => 'nullable|exists:socios,id|unique:users,socio_id',
        ]);

        if ($data['rol'] !== 'socio') {
            $data['socio_id'] = null;
        }

        $data['password'] = Hash::make($data['password']);

        $usuario = User::create($data);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario {$usuario->name} creado exitosamente.");
    }

    public function edit(User $usuario): View
    {
        $socios = Socio::where(function ($q) use ($usuario) {
            $q->whereDoesntHave('usuario');
            if ($usuario->socio_id) {
                $q->orWhere('id', $usuario->socio_id);
            }
        })->orderBy('apellido')->orderBy('nombre')->get();

        return view('usuarios.edit', compact('usuario', 'socios'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:150', Rule::unique('users')->ignore($usuario->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'rol'      => 'required|in:desarrollador,administracion,socio',
            'socio_id' => ['nullable', 'exists:socios,id', Rule::unique('users', 'socio_id')->ignore($usuario->id)],
        ]);

        if ($data['rol'] !== 'socio') {
            $data['socio_id'] = null;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario {$usuario->name} actualizado correctamente.");
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No podés eliminar tu propia cuenta.');
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario {$nombre} eliminado.");
    }
}
