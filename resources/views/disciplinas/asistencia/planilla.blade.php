@extends('layouts.app')

@section('title', 'Asistencia — ' . $disciplina->nombre)

@section('content')

{{-- Breadcrumb --}}
<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('disciplinas.index') }}" class="hover:text-slate-700 transition-colors">Disciplinas</a>
    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <a href="{{ route('disciplinas.show', $disciplina) }}" class="hover:text-slate-700 transition-colors">{{ $disciplina->nombre }}</a>
    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Asistencia</span>
</div>


{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Asistencia — {{ $disciplina->nombre }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">Planilla mensual de presencias por clase.</p>
    </div>
    <a href="{{ route('disciplinas.asistencia.tomar', $disciplina) }}"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Tomar lista hoy
    </a>
</div>

{{-- Selector de mes --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-5 flex items-center gap-3">
    <label class="text-sm font-medium text-slate-700 shrink-0">Período:</label>
    <form method="GET" action="{{ route('disciplinas.asistencia.planilla', $disciplina) }}">
        <select name="mes" onchange="this.form.submit()"
            class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            @foreach($mesList as $valor => $etiqueta)
                <option value="{{ $valor }}" {{ $mes === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- Planilla --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

    @if($fechas->isEmpty())
        <div class="px-5 py-16 text-center">
            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-900 mb-1">Sin registros este mes</p>
            <p class="text-xs text-slate-400 mb-4">Todavía no se tomó asistencia para este período.</p>
            <a href="{{ route('disciplinas.asistencia.tomar', $disciplina) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                Tomar lista hoy
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="sticky left-0 bg-slate-50 z-10 text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap border-r border-slate-200 min-w-48">
                            Socio
                        </th>
                        @foreach($fechas as $fecha)
                            @php $carbon = \Carbon\Carbon::parse($fecha); @endphp
                            <th class="px-2 py-3 text-center text-xs font-semibold text-slate-500 whitespace-nowrap w-12 min-w-12">
                                <a href="{{ route('disciplinas.asistencia.tomar', [$disciplina, 'fecha' => $fecha]) }}"
                                    class="flex flex-col items-center gap-0.5 group hover:text-blue-600 transition-colors"
                                    title="{{ $carbon->locale('es')->isoFormat('dddd D [de] MMMM') }}">
                                    <span class="text-xs font-bold group-hover:text-blue-600">{{ $carbon->format('d') }}</span>
                                    <span class="text-[10px] font-normal opacity-70">{{ mb_strtoupper($carbon->locale('es')->isoFormat('dd')) }}</span>
                                </a>
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap border-l border-slate-200">
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($socios as $socio)
                        @php
                            $asistidas = collect($mapa[$socio->id] ?? [])->count();
                            $total     = $fechas->count();
                            $pct       = $total > 0 ? round($asistidas / $total * 100) : 0;
                            $pctColor  = $pct >= 75
                                ? 'text-green-700 bg-green-50 border-green-200'
                                : ($pct >= 50 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-red-700 bg-red-50 border-red-200');
                        @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="sticky left-0 bg-white hover:bg-slate-50/50 z-10 px-5 py-3 border-r border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($socio->fotoUrl())
                                            <img src="{{ $socio->fotoUrl() }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xs font-semibold text-slate-500">
                                                {{ mb_strtoupper(mb_substr($socio->nombre, 0, 1) . mb_substr($socio->apellido, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('socios.show', $socio) }}" class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors block truncate">
                                            {{ $socio->nombreCompleto() }}
                                        </a>
                                        <span class="text-xs text-slate-400">N° {{ $socio->numero_socio }}</span>
                                    </div>
                                </div>
                            </td>
                            @foreach($fechas as $fecha)
                                @php $estuvo = !empty($mapa[$socio->id][$fecha]); @endphp
                                <td class="px-2 py-3 text-center">
                                    @if($estuvo)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-100 text-green-600" title="Presente">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-7 h-7 text-slate-300" title="Ausente">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                            </svg>
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-3 text-center border-l border-slate-200">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $pctColor }}">
                                    {{ $asistidas }}/{{ $total }}
                                    <span class="ml-1 opacity-75">({{ $pct }}%)</span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- Totales por fecha --}}
                <tfoot>
                    <tr class="bg-slate-50 border-t border-slate-200">
                        <td class="sticky left-0 bg-slate-50 z-10 px-5 py-3 border-r border-slate-200">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Presentes</span>
                        </td>
                        @foreach($fechas as $fecha)
                            @php $presentesFecha = collect($mapa)->filter(fn($dias) => !empty($dias[$fecha]))->count(); @endphp
                            <td class="px-2 py-3 text-center">
                                <span class="text-xs font-bold text-slate-600">{{ $presentesFecha }}</span>
                            </td>
                        @endforeach
                        <td class="px-4 py-3 border-l border-slate-200"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

@endsection
