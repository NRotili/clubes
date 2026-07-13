@extends('layouts.app')

@section('title', $actividad->nombre)

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('actividades.index') }}" class="hover:text-slate-700 transition-colors">Actividades</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">{{ $actividad->nombre }}</span>
</div>

{{-- Encabezado --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ $actividad->nombre }}</h1>
                @php
                    $estadoClase = $actividad->estado === 'activa'
                        ? 'bg-green-50 text-green-700 border-green-200'
                        : 'bg-slate-100 text-slate-500 border-slate-200';
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $estadoClase }}">
                    {{ ucfirst($actividad->estado) }}
                </span>
            </div>
            @if($actividad->descripcion)
                <p class="text-sm text-slate-500">{{ $actividad->descripcion }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('actividades.agenda', $actividad) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                </svg>
                Ver agenda
            </a>
            <a href="{{ route('actividades.edit', $actividad) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                </svg>
                Editar
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Info --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Configuración de reservas</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Confirmación</dt>
                <dd class="font-medium text-slate-900">
                    {{ $actividad->requiere_aprobacion ? 'Requiere aprobación' : 'Automática' }}
                </dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Pago previo</dt>
                <dd class="font-medium text-slate-900">
                    {{ $actividad->requiere_pago ? 'Sí' : 'No' }}
                </dd>
            </div>
            @if($actividad->requiere_pago)
                <div class="flex justify-between px-5 py-3 text-sm">
                    <dt class="text-slate-500">Costo por turno</dt>
                    <dd class="font-semibold text-slate-900">${{ number_format($actividad->costo, 2, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Anticipación máxima</dt>
                <dd class="font-medium text-slate-900">
                    {{ $actividad->anticipacion_dias !== null ? $actividad->anticipacion_dias . ' ' . Str::plural('día', $actividad->anticipacion_dias) : 'Sin límite' }}
                </dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Máx. turnos activos por socio</dt>
                <dd class="font-medium text-slate-900">
                    {{ $actividad->max_turnos_activos ?? 'Sin límite' }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Franjas --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden lg:col-span-2">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Franjas de disponibilidad</h2>
        </div>
        @if($actividad->franjas->isEmpty())
            <div class="px-5 py-6 text-center text-sm text-slate-400">Sin franjas cargadas.</div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($actividad->franjas as $franja)
                    <li class="flex items-center justify-between px-5 py-3 text-sm">
                        <span class="font-medium text-slate-900">{{ $franja->etiqueta() }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>

@endsection
