<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class ArtisanController extends Controller
{
    /**
     * Lista blanca de comandos ejecutables desde el panel. No se acepta texto
     * libre: solo estas claves pueden dispararse desde la vista.
     */
    protected const COMANDOS = [
        'cache-clear' => [
            'signature' => 'cache:clear',
            'label' => 'Limpiar caché de aplicación',
            'descripcion' => 'Limpia la caché general de la aplicación.',
            'peligroso' => false,
        ],
        'config-clear' => [
            'signature' => 'config:clear',
            'label' => 'Limpiar caché de configuración',
            'descripcion' => 'Elimina el archivo de configuración cacheado.',
            'peligroso' => false,
        ],
        'route-clear' => [
            'signature' => 'route:clear',
            'label' => 'Limpiar caché de rutas',
            'descripcion' => 'Elimina el archivo de rutas cacheado.',
            'peligroso' => false,
        ],
        'view-clear' => [
            'signature' => 'view:clear',
            'label' => 'Limpiar vistas compiladas',
            'descripcion' => 'Elimina todas las vistas Blade compiladas.',
            'peligroso' => false,
        ],
        'optimize-clear' => [
            'signature' => 'optimize:clear',
            'label' => 'Limpiar todos los cachés',
            'descripcion' => 'Limpia config, rutas, vistas y eventos cacheados de una sola vez.',
            'peligroso' => false,
        ],
        'queue-restart' => [
            'signature' => 'queue:restart',
            'label' => 'Reiniciar workers de la cola',
            'descripcion' => 'Señala a los workers de la cola para que finalicen el job actual y se reinicien.',
            'peligroso' => false,
        ],
        'migrate' => [
            'signature' => 'migrate',
            'params' => ['--force' => true],
            'label' => 'Ejecutar migraciones pendientes',
            'descripcion' => 'Corre las migraciones de base de datos pendientes.',
            'peligroso' => true,
        ],
        'notificar-vencimientos' => [
            'signature' => 'socios:notificar-vencimientos',
            'label' => 'Notificar vencimientos de cuota',
            'descripcion' => 'Envía las push notifications de cuotas próximas a vencer, vencidas hoy y vencidas ayer.',
            'peligroso' => false,
        ],
        'suspender-deudores-simular' => [
            'signature' => 'socios:suspender-deudores',
            'params' => ['--dry-run' => true],
            'label' => 'Suspender deudores (simulación)',
            'descripcion' => 'Muestra quiénes serían suspendidos por deuda, sin aplicar cambios.',
            'peligroso' => false,
        ],
        'suspender-deudores-aplicar' => [
            'signature' => 'socios:suspender-deudores',
            'label' => 'Suspender deudores (aplicar)',
            'descripcion' => 'Suspende definitivamente a los socios deudores.',
            'peligroso' => true,
        ],
    ];

    public function index(): View
    {
        return view('artisan.index', [
            'comandos' => self::COMANDOS,
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        $request->validate([
            'comando' => 'required|string',
        ]);

        $clave = $request->input('comando');

        abort_unless(array_key_exists($clave, self::COMANDOS), 404);

        $comando = self::COMANDOS[$clave];

        Artisan::call($comando['signature'], $comando['params'] ?? []);

        return redirect()->route('artisan.index')
            ->with('artisan_label', $comando['label'])
            ->with('artisan_output', trim(Artisan::output()));
    }
}
