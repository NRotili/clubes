@extends('layouts.app')

@section('title', 'Profesores')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Profesores</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $profesores->total() }} registros encontrados</p>
    </div>
    <a href="{{ route('profesores.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm whitespace-nowrap">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo profesor
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('profesores.index') }}" class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar por nombre, apellido o CUIL…"
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
        </div>
        <select name="estado"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-700">
            <option value="">Todos los estados</option>
            <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">Filtrar</button>
            @if(request()->hasAny(['buscar', 'estado']))
                <a href="{{ route('profesores.index') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 text-sm font-medium border border-slate-300 rounded-lg transition-colors">Limpiar</a>
            @endif
        </div>
    </div>
</form>

@if($profesores->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <p class="text-slate-500">No hay profesores registrados.</p>
        <a href="{{ route('profesores.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Registrar el primero</a>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Nombre</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Contacto</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">CUIL</th>
                        <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Disciplinas</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Estado</th>
                        <th class="px-4 py-3 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($profesores as $profesor)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('profesores.show', $profesor) }}" class="font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                    {{ $profesor->nombreCompleto() }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $profesor->celular ?: $profesor->telefono ?: $profesor->email ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-xs">
                                {{ $profesor->cuil ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold
                                    {{ $profesor->disciplinas_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $profesor->disciplinas_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                    {{ $profesor->estado === 'activo' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                    {{ \App\Models\Profesor::etiquetaEstado($profesor->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('profesores.show', $profesor) }}" title="Ver"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('profesores.edit', $profesor) }}" title="Editar"
                                        class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($profesores->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $profesores->links() }}
            </div>
        @endif
    </div>
@endif

@endsection
