@extends('layouts.app')

@section('title', 'Configuración de Cuotas')

@section('content')

@php
    $etiquetasCat = [
        'adulto'   => 'Adulto',
        'junior'   => 'Junior',
        'cadete'   => 'Cadete',
        'bebe'     => 'Bebé',
        'jubilado' => 'Jubilado',
    ];
    $etiquetasGen = [
        'M' => 'Masculino',
        'F' => 'Femenino',
        'X' => 'No binario / Otro',
    ];
@endphp

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <span class="text-slate-400">Configuración</span>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Cuotas</span>
</div>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Configuración de Cuotas</h1>
        <p class="text-sm text-slate-500 mt-0.5">Monto mensual base por categoría y género.</p>
    </div>
</div>

<form method="POST" action="{{ route('cuotas.config.update') }}">
    @csrf

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4 w-40">
                            Categoría
                        </th>
                        @foreach($generos as $gen)
                            <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">
                                {{ $etiquetasGen[$gen] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($categorias as $cat)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $etiquetasCat[$cat] }}
                            </td>
                            @foreach($generos as $gen)
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        <div class="flex rounded-lg border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent w-40">
                                            <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-r border-slate-300 select-none">$</span>
                                            <input
                                                type="number"
                                                name="cuotas[{{ $cat }}][{{ $gen }}]"
                                                value="{{ old("cuotas.{$cat}.{$gen}", $grilla[$cat][$gen]) }}"
                                                min="0"
                                                step="0.01"
                                                class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none text-right w-0">
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Nota informativa --}}
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-500">
                Estos montos representan la cuota base mensual de membresía.
                A este valor se suman las cuotas de las disciplinas en las que el socio esté inscripto.
            </p>
        </div>
    </div>

    {{-- Vencimiento y recargo --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-semibold text-slate-700">Vencimiento y recargo por mora</h2>
            <p class="text-xs text-slate-500 mt-0.5">Se aplica a todas las cuotas mensuales.</p>
        </div>
        <div class="p-6 flex flex-col sm:flex-row gap-6">
            <div class="flex-1">
                <label for="dia_vencimiento" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Día de vencimiento <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Día</span>
                    <input type="number" id="dia_vencimiento" name="dia_vencimiento"
                        value="{{ old('dia_vencimiento', $diaVencimiento) }}"
                        min="1" max="28"
                        class="w-20 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center">
                    <span class="text-sm text-slate-500">de cada mes</span>
                </div>
                <p class="text-xs text-slate-400 mt-1.5">Máximo día 28 para cubrir todos los meses.</p>
            </div>
            <div class="flex-1">
                <label for="recargo_mora" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Recargo por mora <span class="text-red-500">*</span>
                </label>
                <div class="flex rounded-lg border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 w-36">
                    <input type="number" id="recargo_mora" name="recargo_mora"
                        value="{{ old('recargo_mora', $recargoMora) }}"
                        min="0" max="100" step="0.1"
                        class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none text-right w-0">
                    <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-l border-slate-300 select-none">%</span>
                </div>
                <p class="text-xs text-slate-400 mt-1.5">0% = sin recargo. Se informa al registrar el pago.</p>
            </div>
        </div>
    </div>

    {{-- Suspensión automática --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-semibold text-slate-700">Suspensión automática por mora</h2>
            <p class="text-xs text-slate-500 mt-0.5">El comando <code class="bg-slate-200 px-1 rounded">socios:suspender-deudores</code> suspende socios que superen este umbral.</p>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-3">
                <div class="flex rounded-lg border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 w-36">
                    <input type="number" name="meses_suspension"
                        value="{{ old('meses_suspension', $mesesSuspension) }}"
                        min="0" max="24"
                        class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none text-right w-0">
                    <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-l border-slate-300 select-none">meses</span>
                </div>
                <span class="text-sm text-slate-500">de deuda acumulada para suspender</span>
            </div>
            <p class="text-xs text-slate-400 mt-2">0 = suspensión automática desactivada. Se puede ejecutar manualmente desde la terminal.</p>

            <div class="mt-4 flex items-center gap-2 text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                <svg class="w-3.5 h-3.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z"/>
                </svg>
                <span>Para ejecutar manualmente: <code class="bg-white border border-slate-300 rounded px-1">php artisan socios:suspender-deudores</code> o con <code class="bg-white border border-slate-300 rounded px-1">--dry-run</code> para previsualizar.</span>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <button type="submit"
            class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            Guardar cambios
        </button>
    </div>
</form>

@endsection
