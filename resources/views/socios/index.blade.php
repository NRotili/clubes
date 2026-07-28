@extends('layouts.app')

@section('title', 'Socios')

@section('content')

{{-- Encabezado de página --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Socios</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $socios->total() }} {{ $socios->total() === 1 ? 'socio registrado' : 'socios registrados' }}
        </p>
    </div>
    @if(auth()->user()->puedeGestionarSocios())
        <div class="flex items-center gap-2">
            <a href="{{ route('socios.importar') }}"
                class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 text-sm font-medium px-4 py-2.5 rounded-lg border border-slate-300 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                </svg>
                Importar
            </a>
            <a href="{{ route('socios.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nuevo Socio
            </a>
        </div>
    @endif
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('socios.index') }}" class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar por nombre, apellido, N° socio o documento…"
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400">
        </div>
        <select name="estado"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-slate-700">
            <option value="">Todos los estados</option>
            <option value="activo"     {{ request('estado') === 'activo'     ? 'selected' : '' }}>Activo</option>
            <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
            <option value="suspendido" {{ request('estado') === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
            <option value="inactivo"   {{ request('estado') === 'inactivo'   ? 'selected' : '' }}>Inactivo</option>
        </select>
        <select name="categoria"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-slate-700">
            <option value="">Todas las categorías</option>
            <option value="adulto"   {{ request('categoria') === 'adulto'   ? 'selected' : '' }}>Adulto</option>
            <option value="junior"   {{ request('categoria') === 'junior'   ? 'selected' : '' }}>Junior</option>
            <option value="cadete"   {{ request('categoria') === 'cadete'   ? 'selected' : '' }}>Cadete</option>
            <option value="bebe"     {{ request('categoria') === 'bebe'     ? 'selected' : '' }}>Bebé</option>
            <option value="jubilado" {{ request('categoria') === 'jubilado' ? 'selected' : '' }}>Jubilado</option>
        </select>
        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['buscar', 'estado', 'categoria']))
                <a href="{{ route('socios.index') }}"
                    class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 text-sm font-medium border border-slate-300 rounded-lg transition-colors">
                    Limpiar
                </a>
            @endif
        </div>
    </div>
</form>

{{-- Tabla --}}
@if($socios->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
        </svg>
        <p class="text-slate-500 font-medium">No se encontraron socios</p>
        @if(request()->hasAny(['buscar', 'estado', 'categoria']))
            <p class="text-slate-400 text-sm mt-1">Probá con otros términos de búsqueda o limpiá los filtros.</p>
        @else
            <a href="{{ route('socios.create') }}" class="inline-block mt-4 text-sm text-blue-600 font-medium hover:underline">
                Registrá el primer socio →
            </a>
        @endif
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="hidden sm:table-cell text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">N° Socio</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Apellido y Nombre</th>
                        <th class="hidden lg:table-cell text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Documento</th>
                        <th class="hidden sm:table-cell text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Categoría</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Estado</th>
                        <th class="hidden lg:table-cell text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Grupo familiar</th>
                        <th class="px-4 py-3 w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($socios as $socio)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="hidden sm:table-cell px-4 py-3 font-mono font-medium text-slate-700">{{ $socio->numero_socio }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('socios.show', $socio) }}" class="font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                    {{ $socio->nombreCompleto() }}
                                </a>
                                <span class="sm:hidden block text-xs text-slate-400 mt-0.5 font-mono">N° {{ $socio->numero_socio }}</span>
                            </td>
                            <td class="hidden lg:table-cell px-4 py-3 text-slate-600">
                                <span class="text-xs text-slate-400">{{ $socio->tipo_documento }}</span>
                                {{ $socio->numero_documento }}
                            </td>
                            <td class="hidden sm:table-cell px-4 py-3">
                                @php
                                    $catClases = [
                                        'adulto'   => 'bg-blue-50 text-blue-700',
                                        'junior'   => 'bg-indigo-50 text-indigo-700',
                                        'cadete'   => 'bg-violet-50 text-violet-700',
                                        'bebe'     => 'bg-pink-50 text-pink-700',
                                        'jubilado' => 'bg-slate-100 text-slate-600',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $catClases[$socio->categoria] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ \App\Models\Socio::etiquetaCategoria($socio->categoria) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $estadoClases = [
                                        'activo'     => 'bg-green-50 text-green-700',
                                        'pendiente'  => 'bg-amber-50 text-amber-700',
                                        'suspendido' => 'bg-orange-50 text-orange-700',
                                        'inactivo'   => 'bg-slate-100 text-slate-500',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium {{ $estadoClases[$socio->estado] ?? 'bg-slate-100 text-slate-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block
                                        {{ $socio->estado === 'activo' ? 'bg-green-500' : ($socio->estado === 'pendiente' ? 'bg-amber-500' : ($socio->estado === 'suspendido' ? 'bg-orange-500' : 'bg-slate-400')) }}">
                                    </span>
                                    {{ \App\Models\Socio::etiquetaEstado($socio->estado) }}
                                </span>
                            </td>
                            <td class="hidden lg:table-cell px-4 py-3 text-slate-500">
                                @if($socio->esTitular())
                                    <span class="text-xs text-slate-400">Titular</span>
                                @else
                                    <span class="text-xs">
                                        Familiar de
                                        <a href="{{ route('socios.show', $socio->titular) }}" class="text-blue-600 hover:underline font-medium">
                                            {{ $socio->titular->nombreCompleto() }}
                                        </a>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('socios.show', $socio) }}"
                                        title="Ver detalle"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </a>
                                    @if(auth()->user()->puedeGestionarSocios())
                                        <a href="{{ route('socios.edit', $socio) }}"
                                            title="Editar"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($socios->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $socios->links() }}
            </div>
        @endif
    </div>
@endif

@endsection
