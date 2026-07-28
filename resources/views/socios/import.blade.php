@extends('layouts.app')

@section('title', 'Importar Socios')

@section('content')

{{-- Encabezado de página --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Importar Socios</h1>
        <p class="text-sm text-slate-500 mt-0.5">Cargá socios masivamente desde un archivo Excel o CSV.</p>
    </div>
    <a href="{{ route('socios.index') }}"
        class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 text-sm font-medium px-4 py-2.5 rounded-lg border border-slate-300 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
        Volver
    </a>
</div>

@if(!$columns)

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- PASO 1: Selección de archivo                           --}}
    {{-- ══════════════════════════════════════════════════════ --}}

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
            </svg>
            <h2 class="text-sm font-semibold text-slate-700">¿Qué columnas necesita el archivo?</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-600 mb-4">
                Podés usar cualquier nombre de columna en tu archivo — en el paso siguiente vas a poder asociar cada columna
                con su campo correspondiente. Los campos marcados con <strong class="text-red-600">*</strong> son obligatorios.
                Los campos con <strong>valores aceptados</strong> solo admiten esas opciones: si una fila trae otro valor,
                esa fila se omite y se informa el motivo al finalizar la importación.
                Si el <strong>número de documento</strong> de una fila ya pertenece a un socio existente, sus datos se actualizan
                en vez de crear un duplicado.
            </p>
            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-2.5">Campo</th>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-2.5">Obligatorio</th>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-2.5">Valores aceptados</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $obligatorios = array_keys($importService->camposObligatorios());
                            $valoresEnum  = $importService->valoresEnum();
                        @endphp
                        @foreach($importService->campos() as $campo => $etiqueta)
                            @continue($campo === '')
                            <tr>
                                <td class="px-4 py-2 text-slate-700">{{ $etiqueta }}</td>
                                <td class="px-4 py-2">
                                    @if(in_array($campo, $obligatorios, true))
                                        <span class="text-red-600 font-semibold">Sí</span>
                                    @else
                                        <span class="text-slate-400">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if(isset($valoresEnum[$campo]))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($valoresEnum[$campo] as $codigo => $etiquetaValor)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono bg-slate-100 text-slate-600"
                                                    title="{{ $etiquetaValor }}">
                                                    {{ $codigo }}
                                                </span>
                                            @endforeach
                                        </div>
                                        @if($campo === 'tipo_documento')
                                            <span class="text-xs text-slate-400">si no se mapea o no coincide, se usa DNI</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <a href="{{ route('socios.importar.plantilla') }}"
                class="inline-flex items-center gap-1.5 mt-4 text-sm text-blue-600 font-medium hover:underline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Descargar plantilla de ejemplo (CSV)
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-semibold text-slate-700">Seleccionar archivo</h2>
        </div>
        <form method="POST" action="{{ route('socios.importar.preview') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <label for="archivo"
                class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-slate-300 rounded-lg p-8 cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition-colors
                    {{ $errors->has('archivo') ? 'border-red-400 bg-red-50' : '' }}">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15"/>
                </svg>
                <span id="archivo-nombre" class="text-sm font-medium text-slate-600">Hacé clic para elegir un archivo o arrastralo acá</span>
                <span class="text-xs text-slate-400">.xlsx · .xls · .csv — máximo 10 MB</span>
                <input type="file" id="archivo" name="archivo" accept=".xlsx,.xls,.csv" class="hidden" required>
            </label>
            @error('archivo')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-5 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                    Continuar
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const input  = document.getElementById('archivo');
            const nombre = document.getElementById('archivo-nombre');
            if (!input) return;
            input.addEventListener('change', function () {
                if (input.files[0]) {
                    nombre.textContent = input.files[0].name;
                }
            });
        })();
    </script>

@else

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- PASO 2: Mapeo de columnas                              --}}
    {{-- ══════════════════════════════════════════════════════ --}}

    @php
        $obligatorios = $importService->camposObligatorios();
        $mapeados     = array_values($mapping);
        $valoresEnum  = $importService->valoresEnum();
    @endphp

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-semibold text-slate-700">Campos obligatorios</h2>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-2">
                @foreach($obligatorios as $campo => $etiqueta)
                    @php $estaMapeado = in_array($campo, $mapeados, true); @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                        {{ $estaMapeado ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $estaMapeado ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $etiqueta }}
                    </span>
                @endforeach
            </div>
            @error('mapping')
                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('archivo')
                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-1.5">
                @foreach($valoresEnum as $campo => $valores)
                    <p class="text-xs text-slate-500">
                        <span class="font-medium text-slate-600">{{ $importService->campos()[$campo] }}:</span>
                        valores aceptados
                        @foreach($valores as $codigo => $etiquetaValor)
                            <code class="bg-slate-100 text-slate-600 px-1 py-0.5 rounded" title="{{ $etiquetaValor }}">{{ $codigo }}</code>@if(!$loop->last), @endif
                        @endforeach
                    </p>
                @endforeach
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('socios.importar.store') }}">
        @csrf
        <input type="hidden" name="archivo" value="{{ $archivo }}">

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Asociar columnas del archivo</h2>
                <span class="text-xs text-slate-400">{{ count($columns) }} {{ count($columns) === 1 ? 'columna detectada' : 'columnas detectadas' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-2.5 w-1/3">Columna del archivo</th>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-2.5 w-1/4">Ejemplo</th>
                            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-2.5">Campo del sistema</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($columns as $col)
                            @php $campoActual = $mapping[$col['formatted']] ?? ''; @endphp
                            <tr class="{{ $campoActual !== '' ? 'bg-blue-50/30' : '' }}">
                                <td class="px-4 py-2.5">
                                    <code class="text-xs bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded">{{ $col['raw'] }}</code>
                                </td>
                                <td class="px-4 py-2.5 text-slate-500 text-xs italic">
                                    {{ $col['sample'] !== '' ? $col['sample'] : '—' }}
                                </td>
                                <td class="px-4 py-2.5">
                                    <select name="mapping[{{ $col['formatted'] }}]"
                                        class="w-full max-w-xs px-2.5 py-1.5 text-sm border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500
                                            {{ $campoActual !== '' ? 'border-blue-400' : 'border-slate-300' }}">
                                        @foreach($importService->campos() as $valor => $etiqueta)
                                            <option value="{{ $valor }}" {{ $campoActual === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center gap-3">
                <a href="{{ route('socios.importar') }}"
                    class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 text-sm font-medium border border-slate-300 rounded-lg transition-colors">
                    Volver
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                    Importar socios
                </button>
            </div>
        </div>
    </form>

@endif

@endsection
