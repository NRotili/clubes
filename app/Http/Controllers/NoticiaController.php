<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticiaController extends Controller
{
    public function index(): View
    {
        abort_if(!auth()->user()->puedeGestionarSocios(), 403);

        $noticias = Noticia::with('autor')->latest()->paginate(20);

        return view('noticias.index', compact('noticias'));
    }

    public function create(): View
    {
        abort_if(!auth()->user()->puedeGestionarSocios(), 403);

        return view('noticias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if(!auth()->user()->puedeGestionarSocios(), 403);

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'cuerpo' => 'required|string|max:5000',
        ]);

        $cuerpoLimpio = strip_tags($data['cuerpo'], '<p><br><strong><em><u><ol><ul><li><a><h1><h2><h3>');

        $noticia = Noticia::create([
            'titulo'        => $data['titulo'],
            'cuerpo'        => $cuerpoLimpio,
            'publicado_por' => auth()->id(),
        ]);

        $tokens = User::whereNotNull('expo_push_token')
            ->pluck('expo_push_token')
            ->all();

        if (!empty($tokens)) {
            PushNotificationService::enviarAVarios(
                $tokens,
                auth()->user()->name . ' publicó nuevas noticias',
                'Abrí la app para leerlas.',
                ['tipo' => 'noticia', 'noticia_id' => (string) $noticia->id]
            );
        }

        return redirect()->route('noticias.index')
            ->with('success', "Noticia publicada y notificación enviada a " . count($tokens) . " socios.");
    }

    public function destroy(Noticia $noticia): RedirectResponse
    {
        abort_if(!auth()->user()->puedeGestionarSocios(), 403);

        $noticia->delete();

        return back()->with('success', 'Noticia eliminada.');
    }
}
