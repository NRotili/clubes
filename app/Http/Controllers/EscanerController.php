<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Socio;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EscanerController extends Controller
{
    public function index(): View
    {
        return view('escaner.index');
    }

    public function check(Request $request): JsonResponse
    {
        $request->validate(['uuid' => 'required|string']);
        $uuid = $request->uuid;

        $socio = Socio::where('qr_uuid', $uuid)->first();

        if (!$socio) {
            return response()->json(['tipo' => 'desconocido', 'mensaje' => 'QR no reconocido.']);
        }

        if (!in_array($socio->estado, ['activo'])) {
            return response()->json([
                'tipo'    => 'inactivo',
                'mensaje' => 'Socio ' . Socio::etiquetaEstado($socio->estado),
                'socio'   => $this->socioData($socio),
            ]);
        }

        $ahora   = Carbon::now();
        $reciente = Ingreso::where('socio_id', $socio->id)
            ->where('ingresado_en', '>=', $ahora->copy()->subMinutes(10))
            ->latest('ingresado_en')
            ->first();

        if ($reciente) {
            return response()->json([
                'tipo'       => 'duplicado',
                'mensaje'    => 'Ya registrado a las ' . $reciente->ingresado_en->format('H:i'),
                'socio'      => $this->socioData($socio),
                'ingreso_en' => $reciente->ingresado_en->format('H:i'),
            ]);
        }

        $ingreso = Ingreso::create(['socio_id' => $socio->id, 'ingresado_en' => $ahora]);

        $socio->loadMissing('usuario');
        PushNotificationService::enviarAlSocio(
            $socio,
            '✅ Ingreso registrado',
            'Tu ingreso al club fue registrado a las ' . $ingreso->ingresado_en->format('H:i') . '.',
            ['tipo' => 'ingreso']
        );

        return response()->json([
            'tipo'       => 'ok',
            'mensaje'    => 'Ingreso registrado',
            'socio'      => $this->socioData($socio),
            'ingreso_en' => $ingreso->ingresado_en->format('H:i'),
        ]);
    }

    private function socioData(Socio $socio): array
    {
        return [
            'nombre'    => $socio->nombreCompleto(),
            'numero'    => $socio->numero_socio,
            'categoria' => Socio::etiquetaCategoria($socio->categoria),
            'estado'    => $socio->estado,
            'foto_url'  => $socio->fotoUrl(),
            'iniciales' => strtoupper(substr($socio->nombre, 0, 1) . substr($socio->apellido, 0, 1)),
        ];
    }
}
