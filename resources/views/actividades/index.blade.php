@extends('layouts.app')

@section('title', 'Actividades')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Actividades e instalaciones</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $actividades->count() }} {{ $actividades->count() === 1 ? 'actividad registrada' : 'actividades registradas' }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('actividades.turnos.pendientes') }}"
            class="inline-flex items-center gap-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            Solicitudes
        </a>
        <a href="{{ route('actividades.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nueva Actividad
        </a>
    </div>
</div>

@if($actividades->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
        </svg>
        <p class="text-slate-500 font-medium">No hay actividades registradas</p>
        <a href="{{ route('actividades.create') }}" class="inline-block mt-4 text-sm text-blue-600 font-medium hover:underline">
            Creá la primera actividad →
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($actividades as $actividad)
            @php
                $estadoClase = $actividad->estado === 'activa'
                    ? 'bg-green-50 text-green-700 border-green-200'
                    : 'bg-slate-100 text-slate-500 border-slate-200';
            @endphp
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 flex-1">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h2 class="font-semibold text-slate-900 text-base leading-tight">{{ $actividad->nombre }}</h2>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $estadoClase }}">
                            {{ ucfirst($actividad->estado) }}
                        </span>
                    </div>

                    @if($actividad->descripcion)
                        <p class="text-xs text-slate-500 mb-3 leading-relaxed line-clamp-2">{{ $actividad->descripcion }}</p>
                    @endif

                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @if($actividad->requiere_aprobacion)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-amber-50 text-amber-700 border border-amber-100">
                                Requiere aprobación
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-green-50 text-green-700 border border-green-100">
                                Confirmación automática
                            </span>
                        @endif
                        @if($actividad->requiere_pago)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-blue-50 text-blue-700 border border-blue-100">
                                ${{ number_format($actividad->costo, 2, ',', '.') }} / turno
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 text-sm">
                        <div>
                            <span class="text-slate-400 text-xs">Franjas</span>
                            <p class="font-semibold text-slate-900">{{ $actividad->franjas_count }}</p>
                        </div>
                        @if($actividad->anticipacion_dias !== null)
                            <div>
                                <span class="text-slate-400 text-xs">Anticipación</span>
                                <p class="font-semibold text-slate-900">{{ $actividad->anticipacion_dias }} {{ Str::plural('día', $actividad->anticipacion_dias) }}</p>
                            </div>
                        @endif
                        @if($actividad->max_turnos_activos !== null)
                            <div>
                                <span class="text-slate-400 text-xs">Máx. activos</span>
                                <p class="font-semibold text-slate-900">{{ $actividad->max_turnos_activos }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-100 px-5 py-3 bg-slate-50 flex items-center justify-between">
                    <a href="{{ route('actividades.show', $actividad) }}"
                        class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        Ver detalle →
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('actividades.agenda', $actividad) }}"
                            class="text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                            Agenda
                        </a>
                        <a href="{{ route('actividades.edit', $actividad) }}"
                            class="text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
