<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Acceso — {{ $socio->nombreCompleto() }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">

    @php
        $puedeIngresar = $socio->estado === 'activo';

        if (!$puedeIngresar) {
            $colorBanner = 'bg-red-50 border-red-300';
            $colorTexto  = 'text-red-800';
            $icono       = '✕';
            $mensaje     = 'ACCESO DENEGADO';
        } elseif ($esNuevo) {
            $colorBanner = 'bg-green-50 border-green-300';
            $colorTexto  = 'text-green-800';
            $icono       = '✓';
            $mensaje     = 'ACCESO PERMITIDO';
        } else {
            $colorBanner = 'bg-amber-50 border-amber-300';
            $colorTexto  = 'text-amber-800';
            $icono       = '⚠';
            $mensaje     = 'YA REGISTRADO';
        }
    @endphp

    <div class="w-full max-w-sm">

        {{-- Banner principal --}}
        <div class="border-2 rounded-2xl p-6 text-center mb-4 {{ $colorBanner }}">
            <div class="text-5xl font-black mb-2 {{ $colorTexto }}">{{ $icono }}</div>
            <div class="text-lg font-bold tracking-widest {{ $colorTexto }}">{{ $mensaje }}</div>
            @if(!$esNuevo && $puedeIngresar)
                <p class="text-sm mt-2 {{ $colorTexto }} opacity-75">
                    Ingreso registrado a las {{ $ingreso->ingresado_en->format('H:i') }}
                </p>
            @endif
        </div>

        {{-- Datos del socio --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- Foto + nombre --}}
            <div class="flex items-center gap-4 p-5 border-b border-slate-100">
                <div class="w-16 h-16 rounded-full overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center">
                    @if($socio->fotoUrl())
                        <img src="{{ $socio->fotoUrl() }}" alt="Foto" class="w-full h-full object-cover">
                    @else
                        <span class="text-xl font-bold text-slate-500">
                            {{ mb_strtoupper(mb_substr($socio->nombre, 0, 1) . mb_substr($socio->apellido, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <div>
                    <p class="font-bold text-slate-900 text-lg leading-tight">{{ $socio->nombreCompleto() }}</p>
                    <p class="text-sm text-slate-500 font-mono mt-0.5">N° {{ $socio->numero_socio }}</p>
                </div>
            </div>

            {{-- Datos --}}
            <dl class="divide-y divide-slate-100 text-sm">
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-slate-500">{{ $socio->tipo_documento }}</dt>
                    <dd class="font-medium text-slate-900">{{ $socio->numero_documento }}</dd>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-slate-500">Categoría</dt>
                    <dd class="font-medium text-slate-900">{{ \App\Models\Socio::etiquetaCategoria($socio->categoria) }}</dd>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-slate-500">Estado</dt>
                    <dd class="font-semibold {{ $colorTexto }}">{{ \App\Models\Socio::etiquetaEstado($socio->estado) }}</dd>
                </div>
                @if(!$socio->esTitular() && $socio->titular)
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-slate-500">Titular</dt>
                    <dd class="font-medium text-slate-900">{{ $socio->titular->nombreCompleto() }}</dd>
                </div>
                @endif
                @if($puedeIngresar && $esNuevo)
                <div class="flex justify-between px-5 py-3">
                    <dt class="text-slate-500">Ingreso registrado</dt>
                    <dd class="font-medium text-slate-900">{{ $ingreso->ingresado_en->format('H:i') }}</dd>
                </div>
                @endif
            </dl>

            {{-- Timestamp --}}
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400">{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

    </div>

</body>
</html>
