<?php

namespace App\Http\Controllers;

use App\Models\CuotaConfig;
use App\Models\DisciplinaInscripcionLog;
use App\Models\Ingreso;
use App\Models\Socio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SocioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Socio::with('titular')->orderBy('apellido')->orderBy('nombre');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido', 'like', "%{$buscar}%")
                    ->orWhere('numero_socio', 'like', "%{$buscar}%")
                    ->orWhere('numero_documento', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $socios = $query->paginate(20)->withQueryString();

        return view('socios.index', compact('socios'));
    }

    public function create(Request $request): View
    {
        $titulares = Socio::titulares()->get();
        $titularId = $request->query('titular_id');

        return view('socios.create', compact('titulares', 'titularId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->reglas());

        $data['qr_uuid'] = Socio::generarQrUuid();

        if (empty($data['socio_titular_id'])) {
            $data['socio_titular_id'] = null;
            $data['parentesco'] = null;
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('socios/fotos', 'public');
        }

        for ($intento = 0; ; $intento++) {
            $data['numero_socio'] = Socio::generarNumeroSocio();
            try {
                $socio = Socio::create($data);
                break;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                if ($intento >= 4 || !str_contains($e->getMessage(), 'numero_socio')) {
                    throw $e;
                }
                \Illuminate\Support\Facades\Log::error("Colisión de numero_socio '{$data['numero_socio']}' al dar de alta un socio, reintentando (intento {$intento}).");
            }
        }

        return redirect()->route('socios.show', $socio)
            ->with('success', "Socio N° {$socio->numero_socio} — {$socio->nombreCompleto()} registrado exitosamente.");
    }

    public function show(Socio $socio): View
    {
        $user = auth()->user();

        // Socios y profesores solo pueden ver su propio perfil
        if (($user->esSocio() || $user->esProfesor()) && $user->socio_id !== $socio->id) {
            abort(403, 'Solo podés ver tu propio perfil.');
        }

        $socio->load(['titular', 'grupoFamiliar', 'disciplinas' => fn($q) => $q->withPivot(['fecha_inscripcion', 'estado'])]);

        $cuotaBase = CuotaConfig::montoParaSocio($socio);

        $logDisciplinas = DisciplinaInscripcionLog::with(['disciplina', 'registradoPor'])
            ->where('socio_id', $socio->id)
            ->latest('created_at')
            ->get();

        return view('socios.show', compact('socio', 'cuotaBase', 'logDisciplinas'));
    }

    public function edit(Socio $socio): View
    {
        $titulares = Socio::titulares()->where('id', '!=', $socio->id)->get();

        return view('socios.edit', compact('socio', 'titulares'));
    }

    public function update(Request $request, Socio $socio): RedirectResponse
    {
        $data = $request->validate($this->reglas($socio->id));

        if (empty($data['socio_titular_id']) || $data['socio_titular_id'] == $socio->id) {
            $data['socio_titular_id'] = null;
            $data['parentesco'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($socio->foto) {
                Storage::disk('public')->delete($socio->foto);
            }
            $data['foto'] = $request->file('foto')->store('socios/fotos', 'public');
        }

        if ($request->boolean('eliminar_foto') && $socio->foto) {
            Storage::disk('public')->delete($socio->foto);
            $data['foto'] = null;
        }

        $socio->update($data);

        return redirect()->route('socios.show', $socio)
            ->with('success', "Los datos de {$socio->nombreCompleto()} fueron actualizados correctamente.");
    }

    public function destroy(Socio $socio): RedirectResponse
    {
        $nombre = $socio->nombreCompleto();
        $socio->delete(); // soft delete

        return redirect()->route('socios.index')
            ->with('success', "El socio {$nombre} fue dado de baja. Podés restaurarlo desde la papelera.");
    }

    // ─── Papelera ────────────────────────────────────────────────────────────

    public function trash(Request $request): View
    {
        $query = Socio::onlyTrashed()->orderBy('deleted_at', 'desc');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido', 'like', "%{$buscar}%")
                    ->orWhere('numero_socio', 'like', "%{$buscar}%");
            });
        }

        $socios = $query->paginate(20)->withQueryString();

        return view('socios.trash', compact('socios'));
    }

    public function restore(string $uuid): RedirectResponse
    {
        $socio = Socio::onlyTrashed()->where('qr_uuid', $uuid)->firstOrFail();
        $socio->restore();

        return back()->with('success', "El socio {$socio->nombreCompleto()} fue restaurado correctamente.");
    }

    public function forceDestroy(string $uuid): RedirectResponse
    {
        $socio = Socio::onlyTrashed()->where('qr_uuid', $uuid)->firstOrFail();
        $nombre = $socio->nombreCompleto();

        if ($socio->foto) {
            Storage::disk('public')->delete($socio->foto);
        }

        $socio->forceDelete();

        return back()->with('success', "El socio {$nombre} fue eliminado permanentemente.");
    }

    public function verificar(string $uuid): View
    {
        $socio = Socio::where('qr_uuid', $uuid)->firstOrFail();

        $ahora = now();
        $umbral = 10; // minutos para considerar duplicado

        $ingresoReciente = Ingreso::where('socio_id', $socio->id)
            ->where('ingresado_en', '>=', $ahora->copy()->subMinutes($umbral))
            ->latest('ingresado_en')
            ->first();

        if ($ingresoReciente) {
            $esNuevo = false;
            $ingreso = $ingresoReciente;
        } else {
            $esNuevo = true;
            $ingreso = Ingreso::create([
                'socio_id'    => $socio->id,
                'ingresado_en' => $ahora,
            ]);
        }

        return view('socios.verificar', compact('socio', 'esNuevo', 'ingreso'));
    }

    public function qr(Socio $socio): Response
    {
        $url = route('socios.verificar', $socio->qr_uuid);
        $svg = QrCode::format('svg')->size(200)->generate($url);

        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function reglas(?int $ignorarId = null): array
    {
        return [
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'tipo_documento'   => 'required|in:DNI,PASAPORTE,LC,LE,CI',
            'numero_documento' => "required|string|max:20|unique:socios,numero_documento,{$ignorarId}",
            'fecha_nacimiento' => 'required|date|before:today',
            'genero'           => 'required|in:M,F,X',
            'email'            => 'nullable|email|max:150',
            'telefono'         => 'nullable|string|max:20',
            'celular'          => 'nullable|string|max:20',
            'direccion'        => 'nullable|string|max:255',
            'ciudad'           => 'nullable|string|max:100',
            'provincia'        => 'nullable|string|max:100',
            'codigo_postal'    => 'nullable|string|max:10',
            'categoria'        => 'required|in:adulto,junior,cadete,bebe,jubilado',
            'estado'           => 'required|in:activo,inactivo,suspendido,pendiente',
            'fecha_alta'       => 'required|date',
            'socio_titular_id' => 'nullable|exists:socios,id',
            'parentesco'       => 'nullable|required_with:socio_titular_id|in:conyuge,hijo,padre,hermano,otro',
            'observaciones'    => 'nullable|string|max:1000',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'eliminar_foto'    => 'nullable|boolean',
            'paga_cuota_base'  => 'nullable|boolean',
        ];
    }
}
