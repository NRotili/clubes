@extends('layouts.app')

@section('title', 'Solicitudes de turnos')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('actividades.index') }}" class="hover:text-slate-700 transition-colors">Actividades</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Solicitudes</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Solicitudes de turnos</h1>
    <p class="text-sm text-slate-500 mt-0.5">
        {{ $turnos->count() }} {{ $turnos->count() === 1 ? 'solicitud pendiente' : 'solicitudes pendientes' }} de aprobación
    </p>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if($turnos->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
        </svg>
        <p class="text-slate-500 font-medium">No hay solicitudes pendientes</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <ul class="divide-y divide-slate-100">
            @foreach($turnos as $turno)
                <li class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 py-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('actividades.show', $turno->actividad) }}" class="text-sm font-semibold text-slate-900 hover:text-blue-600 transition-colors">
                                {{ $turno->actividad->nombre }}
                            </a>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                Pendiente
                            </span>
                            @if($turno->monto !== null)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs border
                                    {{ $turno->pagado ? 'bg-green-50 text-green-700 border-green-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                    ${{ number_format($turno->monto, 2, ',', '.') }} {{ $turno->pagado ? 'pagado' : 'pendiente de pago' }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600 mt-1">
                            <a href="{{ route('socios.show', $turno->socio) }}" class="font-medium hover:text-blue-600 transition-colors">
                                {{ $turno->socio->nombreCompleto() }}
                            </a>
                            &middot; {{ $turno->fecha->format('d/m/Y') }} &middot; {{ substr($turno->hora_inicio, 0, 5) }} – {{ substr($turno->hora_fin, 0, 5) }}
                        </p>
                        @if($turno->observaciones)
                            <p class="text-xs text-slate-400 mt-1">{{ $turno->observaciones }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <form method="POST" action="{{ route('turnos.aprobar', $turno) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                                Aprobar
                            </button>
                        </form>
                        <form method="POST" action="{{ route('turnos.rechazar', $turno) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                Rechazar
                            </button>
                        </form>
                        <a href="{{ route('actividades.agenda', ['actividad' => $turno->actividad, 'fecha' => $turno->fecha->format('Y-m-d')]) }}"
                            class="text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors whitespace-nowrap">
                            Ver agenda
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@endsection
