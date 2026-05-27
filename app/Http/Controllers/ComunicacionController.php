<?php

namespace App\Http\Controllers;

use App\Mail\ComunicacionMail;
use App\Models\Comunicacion;
use App\Models\Socio;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComunicacionController extends Controller
{
    public function index(): View
    {
        $historial = Comunicacion::with('usuario')
            ->latest()
            ->take(30)
            ->get();

        $categorias = ['adulto', 'junior', 'cadete', 'bebe', 'jubilado'];

        return view('comunicaciones.index', compact('historial', 'categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'asunto'            => 'required|string|max:200',
            'cuerpo'            => 'required|string',
            'destinatario_tipo' => 'required|in:todos,deudores,categoria,socio',
            'filtro'            => 'nullable|string',
        ]);

        $destinatarios = $this->resolverDestinatarios(
            $data['destinatario_tipo'],
            $data['filtro'] ?? null
        );

        if ($destinatarios->isEmpty()) {
            return back()->with('error', 'No se encontraron destinatarios con los filtros seleccionados.')->withInput();
        }

        $enviados = 0;
        $fallidos = 0;

        foreach ($destinatarios as $socio) {
            if (!$socio->email) { $fallidos++; continue; }
            try {
                Mail::to($socio->email)->queue(
                    new ComunicacionMail($data['asunto'], $data['cuerpo'], $socio->nombre)
                );
                $enviados++;
            } catch (\Throwable) {
                $fallidos++;
            }
        }

        Comunicacion::create([
            'usuario_id'        => auth()->id(),
            'asunto'            => $data['asunto'],
            'cuerpo'            => $data['cuerpo'],
            'tipo'              => 'masiva',
            'destinatario_tipo' => $data['destinatario_tipo'],
            'filtro'            => $data['filtro'] ?? null,
            'enviados'          => $enviados,
            'fallidos'          => $fallidos,
        ]);

        // Push notifications a los que tengan token FCM
        $tokens = $destinatarios
            ->filter(fn($s) => $s->usuario?->expo_push_token)
            ->map(fn($s) => $s->usuario->expo_push_token)
            ->values()
            ->toArray();

        if (!empty($tokens)) {
            PushNotificationService::enviarAVarios(
                $tokens,
                $data['asunto'],
                Str::limit(strip_tags($data['cuerpo']), 120),
                ['tipo' => 'comunicacion']
            );
        }

        $msg = "Comunicación encolada para {$enviados} " . ($enviados === 1 ? 'destinatario' : 'destinatarios') . '.';
        if ($fallidos > 0) $msg .= " {$fallidos} sin email válido.";

        return back()->with('success', $msg);
    }

    public function storeSocio(Request $request, Socio $socio): RedirectResponse
    {
        $data = $request->validate([
            'asunto' => 'required|string|max:200',
            'cuerpo' => 'required|string',
        ]);

        if (!$socio->email) {
            return back()->with('error', 'El socio no tiene email registrado.');
        }

        Mail::to($socio->email)->queue(
            new ComunicacionMail($data['asunto'], $data['cuerpo'], $socio->nombre)
        );

        Comunicacion::create([
            'usuario_id'        => auth()->id(),
            'asunto'            => $data['asunto'],
            'cuerpo'            => $data['cuerpo'],
            'tipo'              => 'individual',
            'destinatario_tipo' => 'socio',
            'filtro'            => (string) $socio->id,
            'enviados'          => 1,
            'fallidos'          => 0,
        ]);

        PushNotificationService::enviarAlSocio(
            $socio->loadMissing('usuario'),
            $data['asunto'],
            Str::limit(strip_tags($data['cuerpo']), 120),
            ['tipo' => 'comunicacion']
        );

        return back()->with('success', "Email enviado a {$socio->nombre} {$socio->apellido}.");
    }

    private function resolverDestinatarios(string $tipo, ?string $filtro)
    {
        return match ($tipo) {
            'todos'    => Socio::where('estado', 'activo')->whereNotNull('email')->with('usuario')->get(),
            'deudores' => Socio::where('estado', 'activo')
                            ->whereHas('cuotasMensuales', fn($q) => $q->whereIn('estado', ['pendiente', 'parcial']))
                            ->whereNotNull('email')
                            ->with('usuario')
                            ->get(),
            'categoria' => Socio::where('estado', 'activo')
                            ->where('categoria', $filtro)
                            ->whereNotNull('email')
                            ->with('usuario')
                            ->get(),
            default    => collect(),
        };
    }
}
