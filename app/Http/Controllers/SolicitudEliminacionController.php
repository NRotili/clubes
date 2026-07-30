<?php

namespace App\Http\Controllers;

use App\Models\SolicitudEliminacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudEliminacionController extends Controller
{
    // ─── Público: cualquiera puede pedir la eliminación de su cuenta ──────────

    public function create(): View
    {
        return view('legal.eliminar-cuenta');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:150',
            'identificador' => 'required|string|max:150',
            'motivo'        => 'nullable|string|max:1000',
        ], [], [
            'nombre'        => 'nombre',
            'identificador' => 'email, DNI o número de socio',
            'motivo'        => 'motivo',
        ]);

        SolicitudEliminacion::create($data);

        return redirect()->route('cuenta.eliminar')
            ->with('success', 'Recibimos tu solicitud. Un administrador del club se va a poner en contacto para confirmar la baja de tu cuenta y tus datos.');
    }

    // ─── Administración: ver y procesar solicitudes ────────────────────────────

    public function index(Request $request): View
    {
        $query = SolicitudEliminacion::with('procesadaPor')->orderByDesc('created_at');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->paginate(20)->withQueryString();
        $pendientes  = SolicitudEliminacion::where('estado', 'pendiente')->count();

        return view('solicitudes-eliminacion.index', compact('solicitudes', 'pendientes'));
    }

    public function procesar(Request $request, SolicitudEliminacion $solicitud): RedirectResponse
    {
        $solicitud->marcarProcesada($request->user());

        return back()->with('success', 'Solicitud marcada como procesada.');
    }
}
