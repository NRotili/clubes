@extends('layouts.app')

@section('title', $profesor->nombreCompleto())

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('profesores.index') }}" class="hover:text-slate-700 transition-colors">Profesores</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">{{ $profesor->nombreCompleto() }}</span>
</div>

{{-- Encabezado --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ $profesor->nombreCompleto() }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                    {{ $profesor->estado === 'activo' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                    {{ \App\Models\Profesor::etiquetaEstado($profesor->estado) }}
                </span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-500 mt-1">
                @if($profesor->email)
                    <span>{{ $profesor->email }}</span>
                @endif
                @if($profesor->celular)
                    <span>{{ $profesor->celular }}</span>
                @endif
                @if($profesor->cuil)
                    <span class="font-mono">CUIL {{ $profesor->cuil }}</span>
                @endif
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('profesores.edit', $profesor) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                </svg>
                Editar
            </a>
            <form method="POST" action="{{ route('profesores.destroy', $profesor) }}">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                    onclick="return confirm('¿Eliminar al profesor {{ addslashes($profesor->nombreCompleto()) }}?')">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</div>

@if($profesor->observaciones)
<div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3.5 mb-6 text-sm text-amber-800">
    {{ $profesor->observaciones }}
</div>
@endif

{{-- Disciplinas --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Disciplinas asignadas
            @if($profesor->disciplinas->count())
                <span class="ml-2 bg-slate-200 text-slate-600 text-xs font-medium px-1.5 py-0.5 rounded-full">{{ $profesor->disciplinas->count() }}</span>
            @endif
        </h2>
    </div>
    @if($profesor->disciplinas->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-slate-400">
            No tiene disciplinas asignadas aún.
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($profesor->disciplinas as $d)
                <li class="flex items-center justify-between px-5 py-3.5">
                    <a href="{{ route('disciplinas.show', $d) }}" class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors">
                        {{ $d->nombre }}
                    </a>
                    <span class="text-sm font-semibold text-slate-700">${{ number_format($d->pivot->sueldo, 2, ',', '.') }}/mes</span>
                </li>
            @endforeach
        </ul>
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between text-sm font-semibold">
            <span class="text-slate-600">Total mensual</span>
            <span class="text-slate-900">${{ number_format($profesor->disciplinas->sum('pivot.sueldo'), 2, ',', '.') }}</span>
        </div>
    @endif
</div>

@endsection
