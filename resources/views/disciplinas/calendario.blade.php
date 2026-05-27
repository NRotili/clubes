@extends('layouts.app')

@section('title', 'Calendario de Disciplinas')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Calendario de Disciplinas</h1>
        <p class="text-sm text-slate-500 mt-0.5">Horarios semanales de disciplinas activas</p>
    </div>
    <a href="{{ route('disciplinas.index') }}"
        class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
        </svg>
        Ver listado
    </a>
</div>

@if($disciplinas->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <p class="text-slate-500">No hay disciplinas activas con horarios cargados.</p>
    </div>
@else

{{-- Leyenda de disciplinas --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3">
    @foreach($disciplinas as $d)
        @php $color = $colorMap[$d->id]; @endphp
        <a href="{{ route('disciplinas.show', $d) }}"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium border transition-opacity hover:opacity-80"
            style="background-color: {{ $color }}18; border-color: {{ $color }}55; color: {{ $color }};">
            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $color }};"></span>
            {{ $d->nombre }}
        </a>
    @endforeach
</div>

{{-- Grilla --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

    @php
        $pxPorMin   = 1.5;          // píxeles por minuto
        $pxPorHora  = $pxPorMin * 60;
        $totalMin   = $maxMin - $minMin;
        $alturaGrid = $totalMin * $pxPorMin;
        $anchoCols  = 7;            // días de la semana
        $etiquetasDias = [
            'lunes'     => 'Lunes',
            'martes'    => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves'    => 'Jueves',
            'viernes'   => 'Viernes',
            'sabado'    => 'Sábado',
            'domingo'   => 'Domingo',
        ];
    @endphp

    {{-- Cabecera de días --}}
    <div class="flex border-b border-slate-200 bg-slate-50 sticky top-16 z-10">
        {{-- Espacio para columna de horas --}}
        <div class="w-14 shrink-0 border-r border-slate-200"></div>
        @foreach($dias as $dia)
            @php $tieneEventos = count($porDia[$dia]) > 0; @endphp
            <div class="flex-1 text-center py-3 text-xs font-semibold uppercase tracking-wider border-r border-slate-200 last:border-r-0
                {{ $tieneEventos ? 'text-slate-700' : 'text-slate-400' }}">
                {{ $etiquetasDias[$dia] }}
            </div>
        @endforeach
    </div>

    {{-- Cuerpo de la grilla --}}
    <div class="flex overflow-x-auto">

        {{-- Columna de horas --}}
        <div class="w-14 shrink-0 border-r border-slate-200 relative" style="height: {{ $alturaGrid }}px;">
            @foreach($horas as $hora)
                @if($hora < $maxMin / 60)
                    <div class="absolute right-0 left-0 flex items-start justify-end pr-2"
                        style="top: {{ ($hora * 60 - $minMin) * $pxPorMin }}px; height: {{ $pxPorHora }}px;">
                        <span class="text-xs text-slate-400 leading-none translate-y-[-0.4em]">{{ str_pad($hora, 2, '0', STR_PAD_LEFT) }}:00</span>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Columnas por día --}}
        @foreach($dias as $dia)
            <div class="flex-1 relative border-r border-slate-200 last:border-r-0 min-w-[110px]"
                style="height: {{ $alturaGrid }}px;">

                {{-- Líneas horarias de fondo --}}
                @foreach($horas as $hora)
                    @if($hora < $maxMin / 60)
                        <div class="absolute left-0 right-0 border-t border-slate-100"
                            style="top: {{ ($hora * 60 - $minMin) * $pxPorMin }}px;"></div>
                    @endif
                @endforeach

                {{-- Eventos --}}
                @php
                    // Detección simple de solapamientos
                    $eventos = $porDia[$dia];
                    $columnas = [];     // track qué columna ocupa cada evento
                    $finesPorCol = [];  // track fin de último evento por columna

                    foreach ($eventos as $idx => $ev) {
                        $col = 0;
                        while (isset($finesPorCol[$col]) && $finesPorCol[$col] > $ev['inicio_min']) {
                            $col++;
                        }
                        $columnas[$idx] = $col;
                        $finesPorCol[$col] = $ev['fin_min'];
                    }
                    $numCols = max(1, count($finesPorCol));
                @endphp

                @foreach($eventos as $idx => $ev)
                    @php
                        $top    = ($ev['inicio_min'] - $minMin) * $pxPorMin;
                        $height = max(($ev['fin_min'] - $ev['inicio_min']) * $pxPorMin, 24);
                        $color  = $colorMap[$ev['disciplina']->id];
                        $col    = $columnas[$idx];
                        $width  = 100 / $numCols;
                        $left   = $col * $width;
                        $corto  = $height < 36;
                    @endphp
                    <a href="{{ route('disciplinas.show', $ev['disciplina']) }}"
                        class="absolute overflow-hidden rounded border hover:opacity-90 transition-opacity"
                        style="
                            top: {{ $top }}px;
                            height: {{ $height }}px;
                            left: calc({{ $left }}% + 2px);
                            width: calc({{ $width }}% - 4px);
                            background-color: {{ $color }}18;
                            border-color: {{ $color }}66;
                        ">
                        <div class="h-full flex flex-col justify-start overflow-hidden px-1.5 py-1"
                            style="border-left: 3px solid {{ $color }};">
                            <p class="text-xs font-semibold leading-tight truncate" style="color: {{ $color }};">
                                {{ $ev['disciplina']->nombre }}
                            </p>
                            @if(!$corto)
                                <p class="text-xs leading-tight mt-0.5 truncate" style="color: {{ $color }}; opacity: .75;">
                                    {{ $ev['hora_inicio'] }} – {{ $ev['hora_fin'] }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach

            </div>
        @endforeach

    </div>
</div>

@endif

@endsection
