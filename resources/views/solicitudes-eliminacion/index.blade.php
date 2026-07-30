@extends('layouts.app')
@section('title', 'Solicitudes de eliminación de cuenta')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Solicitudes de eliminación de cuenta</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ $pendientes }} {{ $pendientes === 1 ? 'solicitud pendiente' : 'solicitudes pendientes' }} de revisión.
                Pedidas desde <a href="{{ route('cuenta.eliminar') }}" target="_blank" class="text-blue-600 hover:underline">{{ url('/eliminar-cuenta') }}</a>.
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Estado</label>
            <select name="estado" onchange="this.form.submit()"
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" @selected(!request('estado'))>Todas</option>
                <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendientes</option>
                <option value="procesada" @selected(request('estado') === 'procesada')>Procesadas</option>
            </select>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nombre</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Email / DNI / N° socio</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Motivo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Fecha</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($solicitudes as $solicitud)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $solicitud->nombre }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $solicitud->identificador }}</td>
                        <td class="px-4 py-3 text-slate-500 hidden md:table-cell max-w-xs truncate" title="{{ $solicitud->motivo }}">
                            {{ $solicitud->motivo ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 hidden sm:table-cell">{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($solicitud->estado === 'pendiente')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Pendiente</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200"
                                    title="Procesada por {{ $solicitud->procesadaPor?->name }} el {{ $solicitud->procesada_en?->format('d/m/Y H:i') }}">
                                    Procesada
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($solicitud->estado === 'pendiente')
                                <form method="POST" action="{{ route('solicitudes-eliminacion.procesar', $solicitud) }}"
                                    onsubmit="return confirm('¿Marcar como procesada? Confirmá esto recién después de haber dado de baja al socio y su cuenta desde la ficha correspondiente.')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                        Marcar procesada
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">No hay solicitudes registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($solicitudes->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>

    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg px-4 py-3 text-sm">
        Marcar una solicitud como "procesada" <strong>no elimina nada automáticamente</strong>. Buscá al socio
        correspondiente (por el email, DNI o N° de socio indicado), verificá su identidad, y dalo de baja desde
        <a href="{{ route('socios.index') }}" class="underline">Socios</a> — desde ahí podés eliminarlo
        definitivamente vía la Papelera si corresponde.
    </div>

</div>
@endsection
