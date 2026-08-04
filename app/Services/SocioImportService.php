<?php

namespace App\Services;

use App\Models\Socio;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SocioImportService
{
    /**
     * Campos del socio que se pueden asociar a una columna del Excel.
     */
    public function campos(): array
    {
        return [
            '' => '— No importar —',
            'numero_socio' => 'Número de socio (opcional, se autogenera si se deja vacío)',
            'apellido' => 'Apellido',
            'nombre' => 'Nombre',
            'tipo_documento' => 'Tipo de documento',
            'numero_documento' => 'Número de documento',
            'fecha_nacimiento' => 'Fecha de nacimiento',
            'genero' => 'Género',
            'email' => 'Correo electrónico',
            'telefono' => 'Teléfono fijo',
            'celular' => 'Celular',
            'direccion' => 'Dirección',
            'ciudad' => 'Ciudad',
            'provincia' => 'Provincia',
            'codigo_postal' => 'Código postal',
            'categoria' => 'Categoría',
            'estado' => 'Estado',
            'fecha_alta' => 'Fecha de alta',
            'observaciones' => 'Observaciones',
        ];
    }

    /**
     * Campos que deben estar mapeados a una columna para poder importar.
     * El resto tiene un valor por defecto razonable si no se mapea o si el
     * valor de la celda no es válido.
     */
    public function camposObligatorios(): array
    {
        return [
            'apellido' => 'Apellido',
            'nombre' => 'Nombre',
            'numero_documento' => 'Número de documento',
            'fecha_nacimiento' => 'Fecha de nacimiento',
            'genero' => 'Género',
            'categoria' => 'Categoría',
            'estado' => 'Estado',
        ];
    }

    /**
     * Valores aceptados para los campos que son enum, para mostrarlos como
     * ayuda antes de importar y evitar filas omitidas por valores inválidos.
     *
     * @return array<string, array<string, string>> campo => [código => etiqueta]
     */
    public function valoresEnum(): array
    {
        return [
            'tipo_documento' => [
                'DNI' => 'DNI',
                'PASAPORTE' => 'Pasaporte',
                'LC' => 'Libreta Cívica',
                'LE' => 'Libreta de Enrolamiento',
                'CI' => 'Cédula de Identidad',
            ],
            'genero' => [
                'M' => 'Masculino',
                'F' => 'Femenino',
                'X' => 'No binario / Otro',
            ],
            'categoria' => [
                'adulto' => 'Adulto',
                'junior' => 'Junior',
                'cadete' => 'Cadete',
                'bebe' => 'Bebé',
                'jubilado' => 'Jubilado',
            ],
            'estado' => [
                'activo' => 'Activo',
                'inactivo' => 'Inactivo',
                'suspendido' => 'Suspendido',
                'pendiente' => 'Pendiente',
            ],
        ];
    }

    private array $autoDetect = [
        'numerosocio' => 'numero_socio',
        'nrosocio' => 'numero_socio',
        'nsocio' => 'numero_socio',
        'nrodesocio' => 'numero_socio',
        'numerodesocio' => 'numero_socio',
        'codigosocio' => 'numero_socio',
        'idsocio' => 'numero_socio',
        'legajo' => 'numero_socio',
        'apellido' => 'apellido',
        'apellidos' => 'apellido',
        'nombre' => 'nombre',
        'nombres' => 'nombre',
        'tipodocumento' => 'tipo_documento',
        'tipodoc' => 'tipo_documento',
        'numerodocumento' => 'numero_documento',
        'nrodocumento' => 'numero_documento',
        'nrodoc' => 'numero_documento',
        'dni' => 'numero_documento',
        'documento' => 'numero_documento',
        'fechanacimiento' => 'fecha_nacimiento',
        'nacimiento' => 'fecha_nacimiento',
        'fecnac' => 'fecha_nacimiento',
        'fechadenacimiento' => 'fecha_nacimiento',
        'genero' => 'genero',
        'sexo' => 'genero',
        'email' => 'email',
        'correo' => 'email',
        'correoelectronico' => 'email',
        'mail' => 'email',
        'telefono' => 'telefono',
        'telefonofijo' => 'telefono',
        'tel' => 'telefono',
        'celular' => 'celular',
        'movil' => 'celular',
        'whatsapp' => 'celular',
        'direccion' => 'direccion',
        'domicilio' => 'direccion',
        'ciudad' => 'ciudad',
        'localidad' => 'ciudad',
        'provincia' => 'provincia',
        'codigopostal' => 'codigo_postal',
        'cp' => 'codigo_postal',
        'categoria' => 'categoria',
        'estado' => 'estado',
        'fechaalta' => 'fecha_alta',
        'fechadealta' => 'fecha_alta',
        'alta' => 'fecha_alta',
        'observaciones' => 'observaciones',
        'notas' => 'observaciones',
        'comentarios' => 'observaciones',
    ];

    /**
     * Lee la fila de encabezados (y una fila de ejemplo) del archivo.
     *
     * @return array{0: array<int, array{raw: string, formatted: string, sample: string}>, 1: array<string, string>}
     */
    public function leerColumnas(string $rutaAbsoluta): array
    {
        $sheet = $this->cargarHoja($rutaAbsoluta);

        $lastCol = $sheet->getHighestDataColumn(1);
        $lastColNum = Coordinate::columnIndexFromString($lastCol);

        $columns = [];
        $mapping = [];

        for ($c = 1; $c <= $lastColNum; $c++) {
            $raw = trim((string) $this->celda($sheet, $c, 1)->getValue());
            if ($raw === '') {
                continue;
            }

            $formatted = Str::slug($raw, '_');
            $sample = trim((string) ($this->celda($sheet, $c, 2)->getValue() ?? ''));

            $columns[] = [
                'raw' => $raw,
                'formatted' => $formatted,
                'sample' => mb_strimwidth($sample, 0, 30, '…'),
            ];

            $normalizado = strtolower(preg_replace('/[\s_\-]+/', '', $raw));
            $mapping[$formatted] = $this->autoDetect[$normalizado] ?? '';
        }

        return [$columns, $mapping];
    }

    /**
     * Importa (crea o actualiza) socios a partir del archivo y el mapeo de columnas.
     * La clave de deduplicación es numero_documento.
     *
     * @param  array<string, string>  $mapping  formatted_key => campo del socio
     * @return array{creados: int, actualizados: int, omitidos: int, errores: array<int, string>}
     */
    public function importar(string $rutaAbsoluta, array $mapping): array
    {
        $sheet = $this->cargarHoja($rutaAbsoluta);

        $colPorCampo = $this->resolverColumnasPorCampo($sheet, $mapping);

        $creados = 0;
        $actualizados = 0;
        $omitidos = 0;
        $errores = [];

        $idsPorDocumento = Socio::pluck('id', 'numero_documento')->all();
        $idsPorNumeroSocio = Socio::pluck('id', 'numero_socio')->all();

        $highestRow = $sheet->getHighestDataRow();

        for ($fila = 2; $fila <= $highestRow; $fila++) {
            $valores = [];
            foreach ($colPorCampo as $campo => $col) {
                $valores[$campo] = trim((string) ($this->celda($sheet, $col, $fila)->getValue() ?? ''));
            }

            if ($this->filaVacia($valores)) {
                continue;
            }

            $numeroDocumento = preg_replace('/[.\s]+/', '', $valores['numero_documento'] ?? '');
            $numeroSocio = $this->normalizarNumeroSocio(trim($valores['numero_socio'] ?? ''));

            $faltantes = [];
            if ($numeroSocio !== '' && mb_strlen($numeroSocio) > 10) {
                $faltantes[] = 'número de socio inválido (máximo 10 caracteres)';
            }
            if (($valores['nombre'] ?? '') === '') {
                $faltantes[] = 'nombre';
            }
            if (($valores['apellido'] ?? '') === '') {
                $faltantes[] = 'apellido';
            }
            if ($numeroDocumento === '') {
                $faltantes[] = 'número de documento';
            }

            $fechaNacimiento = $this->parsearFecha($sheet, $colPorCampo['fecha_nacimiento'] ?? null, $fila);
            if (($colPorCampo['fecha_nacimiento'] ?? null) !== null && $fechaNacimiento === null) {
                $faltantes[] = 'fecha de nacimiento válida';
            }

            $categoria = $this->normalizarCategoria($valores['categoria'] ?? '');
            if ($categoria === null) {
                $faltantes[] = 'categoría válida (adulto/junior/cadete/bebe/jubilado)';
            }

            $genero = $this->normalizarGenero($valores['genero'] ?? '');
            if ($genero === null) {
                $faltantes[] = 'género válido (M/F/X)';
            }

            $estado = $this->normalizarEstado($valores['estado'] ?? '');
            if ($estado === null) {
                $faltantes[] = 'estado válido (activo/inactivo/suspendido/pendiente)';
            }

            if (! empty($faltantes)) {
                $omitidos++;
                $errores[] = "Fila {$fila}: faltan datos obligatorios (".implode(', ', $faltantes).').';

                continue;
            }

            $data = [
                'apellido' => $valores['apellido'],
                'nombre' => $valores['nombre'],
                'numero_documento' => $numeroDocumento,
                'fecha_nacimiento' => $fechaNacimiento,
                'categoria' => $categoria,
                'tipo_documento' => $this->normalizarTipoDocumento($valores['tipo_documento'] ?? ''),
                'genero' => $genero,
                'estado' => $estado,
                'fecha_alta' => $this->parsearFecha($sheet, $colPorCampo['fecha_alta'] ?? null, $fila) ?? now()->format('Y-m-d'),
                'email' => ($valores['email'] ?? '') !== '' ? $valores['email'] : null,
                'telefono' => ($valores['telefono'] ?? '') !== '' ? $valores['telefono'] : null,
                'celular' => ($valores['celular'] ?? '') !== '' ? $valores['celular'] : null,
                'direccion' => ($valores['direccion'] ?? '') !== '' ? $valores['direccion'] : null,
                'ciudad' => ($valores['ciudad'] ?? '') !== '' ? $valores['ciudad'] : null,
                'provincia' => ($valores['provincia'] ?? '') !== '' ? $valores['provincia'] : null,
                'codigo_postal' => ($valores['codigo_postal'] ?? '') !== '' ? $valores['codigo_postal'] : null,
                'observaciones' => ($valores['observaciones'] ?? '') !== '' ? $valores['observaciones'] : null,
            ];

            $socioId = $idsPorDocumento[$numeroDocumento] ?? null;

            $idConMismoNumeroSocio = $numeroSocio !== '' ? ($idsPorNumeroSocio[$numeroSocio] ?? null) : null;
            if ($idConMismoNumeroSocio !== null && $idConMismoNumeroSocio !== $socioId) {
                $omitidos++;
                $errores[] = "Fila {$fila}: el número de socio \"{$numeroSocio}\" ya está en uso por otro socio.";

                continue;
            }

            if ($socioId) {
                if ($numeroSocio !== '') {
                    $data['numero_socio'] = $numeroSocio;
                }
                Socio::whereKey($socioId)->update($data);
                if ($numeroSocio !== '') {
                    $idsPorNumeroSocio[$numeroSocio] = $socioId;
                }
                $actualizados++;
            } else {
                $data['numero_socio'] = $numeroSocio !== '' ? $numeroSocio : Socio::generarNumeroSocio();
                $data['qr_uuid'] = Socio::generarQrUuid();

                $nuevo = Socio::create($data);
                $idsPorDocumento[$numeroDocumento] = $nuevo->id;
                $idsPorNumeroSocio[$nuevo->numero_socio] = $nuevo->id;
                $creados++;
            }
        }

        return compact('creados', 'actualizados', 'omitidos', 'errores');
    }

    private function cargarHoja(string $rutaAbsoluta): Worksheet
    {
        $extension = strtolower(pathinfo($rutaAbsoluta, PATHINFO_EXTENSION));

        $reader = match ($extension) {
            'xlsx' => new Xlsx,
            'xls' => new Xls,
            'csv' => new Csv,
            default => IOFactory::createReaderForFile($rutaAbsoluta),
        };
        $reader->setReadDataOnly(true);

        return $reader->load($rutaAbsoluta)->getActiveSheet();
    }

    private function celda(Worksheet $sheet, int $col, int $row): Cell
    {
        return $sheet->getCell(Coordinate::stringFromColumnIndex($col).$row);
    }

    /**
     * @param  array<string, string>  $mapping  formatted_key => campo
     * @return array<string, int> campo => índice de columna
     */
    private function resolverColumnasPorCampo(Worksheet $sheet, array $mapping): array
    {
        $lastCol = $sheet->getHighestDataColumn(1);
        $lastColNum = Coordinate::columnIndexFromString($lastCol);

        $colPorCampo = [];

        for ($c = 1; $c <= $lastColNum; $c++) {
            $raw = trim((string) $this->celda($sheet, $c, 1)->getValue());
            if ($raw === '') {
                continue;
            }

            $formatted = Str::slug($raw, '_');
            $campo = $mapping[$formatted] ?? '';

            if ($campo !== '') {
                $colPorCampo[$campo] = $c;
            }
        }

        return $colPorCampo;
    }

    private function filaVacia(array $valores): bool
    {
        foreach ($valores as $valor) {
            if (trim((string) $valor) !== '') {
                return false;
            }
        }

        return true;
    }

    private function parsearFecha(Worksheet $sheet, ?int $col, int $fila): ?string
    {
        if ($col === null) {
            return null;
        }

        $cell = $this->celda($sheet, $col, $fila);
        $raw = $cell->getValue();

        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_numeric($raw) && ExcelDate::isDateTime($cell)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $raw))->format('Y-m-d');
            } catch (\Throwable) {
                // sigue con el intento como texto
            }
        }

        $valor = trim((string) $raw);

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y'] as $formato) {
            $fecha = \DateTime::createFromFormat('!'.$formato, $valor);
            if ($fecha && $fecha->format($formato) === $valor) {
                return $fecha->format('Y-m-d');
            }
        }

        try {
            return Carbon::parse($valor)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizarCategoria(string $valor): ?string
    {
        $normalizado = Str::of($valor)->lower()->ascii()->toString();

        return match (true) {
            str_contains($normalizado, 'adult') => 'adulto',
            str_contains($normalizado, 'junior') => 'junior',
            str_contains($normalizado, 'cadete') => 'cadete',
            str_contains($normalizado, 'bebe') => 'bebe',
            str_contains($normalizado, 'jubilad') => 'jubilado',
            default => null,
        };
    }

    /** Aplica el mismo padding a 5 dígitos que Socio::generarNumeroSocio(), para que los números
     *  importados no queden inconsistentes con los generados automáticamente (ej. "23" -> "00023"). */
    private function normalizarNumeroSocio(string $valor): string
    {
        return $valor !== '' && ctype_digit($valor) ? str_pad($valor, 5, '0', STR_PAD_LEFT) : $valor;
    }

    private function normalizarTipoDocumento(string $valor): string
    {
        $normalizado = strtoupper(trim($valor));

        return in_array($normalizado, ['DNI', 'PASAPORTE', 'LC', 'LE', 'CI'], true) ? $normalizado : 'DNI';
    }

    private function normalizarGenero(string $valor): ?string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return null;
        }

        $normalizado = Str::of($valor)->lower()->ascii()->toString();

        return match (true) {
            in_array($normalizado, ['m', 'masculino', 'hombre'], true) => 'M',
            in_array($normalizado, ['f', 'femenino', 'mujer'], true) => 'F',
            in_array($normalizado, ['x', 'otro', 'no binario', 'nobinario'], true) => 'X',
            default => null,
        };
    }

    private function normalizarEstado(string $valor): ?string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return null;
        }

        $normalizado = Str::of($valor)->lower()->ascii()->toString();

        return match (true) {
            str_contains($normalizado, 'inactiv') => 'inactivo',
            str_contains($normalizado, 'pendient') => 'pendiente',
            str_contains($normalizado, 'suspend') => 'suspendido',
            str_contains($normalizado, 'activ') => 'activo',
            default => null,
        };
    }
}
