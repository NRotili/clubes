@extends('layouts.app')

@section('title', 'Papelera — Socios')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('socios.index') }}" class="hover:text-slate-700 transition-colors">Socios</a>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
            </svg>
            <span class="text-slate-900 font-medium">Papelera</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Papelera de socios</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $socios->total() }} {{ $socios->total() === 1 ? 'socio eliminado' : 'socios eliminados' }}
        </p>
    </div>
</div>

{{-- Filtro --}}
<form method="GET" action="{{ route('socios.trash') }}" class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-sm">
    <div class="flex gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar por nombre o N° socio…"
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400">
        </div>
        <button type="submit"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
            Buscar
        </button>
        @if(request('buscar'))
            <a href="{{ route('socios.trash') }}"
                class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                Limpiar
            </a>
        @endif
    </div>
</form>

{{-- Aviso --}}
@if(auth()->user()->esDesarrollador())
    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-4 py-3 text-sm mb-6">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
        <span>Como desarrollador podés restaurar socios o <strong>eliminarlos permanentemente</strong>. La eliminación permanente no tiene retorno.</span>
    </div>
@endif

{{-- Tabla --}}
@if($socios->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
        </svg>
        <p class="text-slate-500 font-medium">La papelera está vacía</p>
        <a href="{{ route('socios.index') }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline font-medium">
            Volver al listado de socios →
        </a>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">N° Socio</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Apellido y Nombre</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Categoría</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Eliminado</th>
                        <th class="px-4 py-3 w-40"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($socios as $socio)
                        <tr class="hover:bg-slate-50 transition-colors opacity-80">
                            <td class="px-4 py-3 font-mono font-medium text-slate-500">{{ $socio->numero_socio }}</td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-700">{{ $socio->nombreCompleto() }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ \App\Models\Socio::etiquetaCategoria($socio->categoria) }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">
                                {{ $socio->deleted_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Restaurar --}}
                                    <form method="POST" action="{{ route('socios.restore', $socio->qr_uuid) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            title="Restaurar socio"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                            </svg>
                                            Restaurar
                                        </button>
                                    </form>

                                    {{-- Eliminar definitivamente (solo desarrollador) --}}
                                    @if(auth()->user()->esDesarrollador())
                                        <form method="POST" action="{{ route('socios.force-destroy', $socio->qr_uuid) }}"
                                            onsubmit="return confirm('¿Eliminar definitivamente a {{ addslashes($socio->nombreCompleto()) }}? Esta acción es irreversible.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                title="Eliminar permanentemente"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                </svg>
                                                Definitivo
                                            </button>
                                        </form>
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
