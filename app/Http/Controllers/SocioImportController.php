<?php

namespace App\Http\Controllers;

use App\Services\SocioImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SocioImportController extends Controller
{
    public function __construct(private SocioImportService $importService) {}

    public function create(): View
    {
        return view('socios.import', [
            'columns' => null,
            'mapping' => [],
            'archivo' => null,
            'importService' => $this->importService,
        ]);
    }

    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'max:10240', $this->reglaExtension()],
        ]);

        $path = $request->file('archivo')->store('imports/socios', 'local');

        try {
            [$columns, $mapping] = $this->importService->leerColumnas(Storage::disk('local')->path($path));
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);

            return back()->withErrors(['archivo' => 'No se pudo leer el archivo: '.$e->getMessage()]);
        }

        if (empty($columns)) {
            Storage::disk('local')->delete($path);

            return back()->withErrors(['archivo' => 'El archivo no tiene columnas con encabezado en la primera fila.']);
        }

        return view('socios.import', [
            'columns' => $columns,
            'mapping' => $mapping,
            'archivo' => $path,
            'importService' => $this->importService,
        ]);
    }

    public function store(Request $request): View|RedirectResponse
    {
        $request->validate([
            'archivo' => 'required|string',
            'mapping' => 'nullable|array',
        ]);

        $path = $request->input('archivo');

        if (! Storage::disk('local')->exists($path)) {
            return redirect()->route('socios.importar')
                ->withErrors(['archivo' => 'El archivo temporal expiró o fue eliminado. Volvé a subirlo.']);
        }

        $mapping = $request->input('mapping', []);
        $rutaAbsoluta = Storage::disk('local')->path($path);

        $faltantes = [];
        foreach ($this->importService->camposObligatorios() as $campo => $etiqueta) {
            if (! in_array($campo, $mapping, true)) {
                $faltantes[] = $etiqueta;
            }
        }

        if (! empty($faltantes)) {
            [$columns] = $this->importService->leerColumnas($rutaAbsoluta);

            return view('socios.import', [
                'columns' => $columns,
                'mapping' => $mapping,
                'archivo' => $path,
                'importService' => $this->importService,
            ])->withErrors(['mapping' => 'Faltan asignar columnas obligatorias: '.implode(', ', $faltantes).'.']);
        }

        $resultado = $this->importService->importar($rutaAbsoluta, $mapping);

        Storage::disk('local')->delete($path);

        $mensaje = "{$resultado['creados']} socios creados, {$resultado['actualizados']} actualizados";
        if ($resultado['omitidos'] > 0) {
            $mensaje .= ", {$resultado['omitidos']} omitidos por datos incompletos";
        }
        $mensaje .= '.';

        if (! empty($resultado['errores'])) {
            $detalle = array_slice($resultado['errores'], 0, 5);
            $mensaje .= ' Detalle: '.implode(' | ', $detalle);
            if (count($resultado['errores']) > 5) {
                $mensaje .= ' | y '.(count($resultado['errores']) - 5).' más.';
            }
        }

        return redirect()->route('socios.index')->with('success', $mensaje);
    }

    /**
     * Valida por extensión de archivo en vez de por MIME detectado por contenido:
     * los CSV de texto plano suelen detectarse como "text/plain" y la regla
     * "mimes:csv" nativa de Laravel los rechaza aunque sean válidos.
     */
    private function reglaExtension(): \Closure
    {
        return function (string $attribute, $value, \Closure $fail) {
            $extension = strtolower($value->getClientOriginalExtension());

            if (! in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
                $fail('El archivo debe tener extensión .xlsx, .xls o .csv.');
            }
        };
    }

    public function plantilla(): Response
    {
        $columnas = array_values(array_filter(
            array_keys($this->importService->campos()),
            fn ($campo) => $campo !== ''
        ));

        $etiquetas = $this->importService->campos();
        unset($etiquetas['']);

        $ejemplo = [
            'apellido' => 'Pérez',
            'nombre' => 'Juan',
            'tipo_documento' => 'DNI',
            'numero_documento' => '30123456',
            'fecha_nacimiento' => '15/03/1985',
            'genero' => 'M',
            'email' => 'juan.perez@ejemplo.com',
            'telefono' => '',
            'celular' => '1123456789',
            'direccion' => 'Av. Siempre Viva 123',
            'ciudad' => 'La Plata',
            'provincia' => 'Buenos Aires',
            'codigo_postal' => '1900',
            'categoria' => 'adulto',
            'estado' => 'activo',
            'fecha_alta' => now()->format('d/m/Y'),
            'observaciones' => '',
        ];

        $filas = [array_values($etiquetas)];
        $filas[] = array_map(fn ($campo) => $ejemplo[$campo] ?? '', $columnas);

        $csv = implode("\n", array_map(
            fn ($fila) => implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', $v).'"', $fila)),
            $filas
        ));

        return response("\xEF\xBB\xBF".$csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_socios.csv"',
        ]);
    }
}
