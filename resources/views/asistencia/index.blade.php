@extends('layouts.app')
@section('title', 'Asistencia')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Asistencia por QR</h1>
        <p class="text-sm text-slate-500 mt-0.5">Registro de ingresos al club escaneados por QR</p>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end bg-white border border-slate-200 rounded-xl p-4">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ $desde }}"
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta }}"
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-slate-600 mb-1">Buscar socio</label>
            <input type="text" name="busqueda" value="{{ $busqueda }}" placeholder="Nombre, apellido o N° socio"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            Filtrar
        </button>
        @if($busqueda || $desde !== \Carbon\Carbon::today()->startOfMonth()->format('Y-m-d'))
            <a href="{{ route('asistencia.index') }}" class="px-4 py-2 text-sm text-slate-500 hover:text-slate-700">Limpiar</a>
        @endif
    </form>

    {{-- Estadísticas del período --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Total ingresos</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalPeriodo }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Promedio diario</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $promedioDia }}</p>
        </div>
        @if($topSocio)
        <div class="bg-white border border-slate-200 rounded-xl p-4 col-span-2">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Socio más activo</p>
            <p class="text-base font-bold text-slate-900 mt-1 truncate">{{ $topSocio->socio->nombreCompleto() }}</p>
            <p class="text-xs text-slate-400">{{ $topSocio->visitas }} visitas en el período</p>
        </div>
        @endif
    </div>

    {{-- Mini barra de asistencia por día --}}
    @if($porDia->count() > 1)
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Distribución por día</p>
        @php $maxBarra = $porDia->max('total') ?: 1; @endphp
        <div class="flex items-end gap-0.5 h-12 overflow-x-auto">
            @foreach($porDia as $dia => $row)
                <div class="flex-shrink-0 flex flex-col items-center gap-0.5 group" style="min-width: 24px">
                    <div class="w-full bg-blue-200 hover:bg-blue-500 rounded-t transition-colors"
                         style="height: {{ max(3, round($row->total / $maxBarra * 40)) }}px"
                         title="{{ $row->total }} ingresos el {{ \Carbon\Carbon::parse($dia)->format('d/m') }}">
                    </div>
                    <span class="text-[8px] text-slate-400">{{ \Carbon\Carbon::parse($dia)->format('d') }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabla de ingresos --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-700">
                {{ $ingresos->total() }} {{ $ingresos->total() === 1 ? 'ingreso' : 'ingresos' }}
            </p>
            <p class="text-xs text-slate-400">Página {{ $ingresos->currentPage() }} de {{ $ingresos->lastPage() }}</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Socio</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">N° socio</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Fecha</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Hora</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($ingresos as $ingreso)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-500 shrink-0">
                                    {{ strtoupper(substr($ingreso->socio->nombre, 0, 1) . substr($ingreso->socio->apellido, 0, 1)) }}
                                </div>
                                <a href="{{ route('socios.show', $ingreso->socio) }}"
                                   class="font-medium text-slate-800 hover:text-blue-600 truncate">
                                    {{ $ingreso->socio->nombreCompleto() }}
                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-500 font-mono hidden sm:table-cell">
                            {{ $ingreso->socio->numero_socio }}
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ $ingreso->ingresado_en->isoFormat('ddd D MMM') }}
                        </td>
                        <td class="px-4 py-3 font-mono font-semibold text-slate-700">
                            {{ $ingreso->ingresado_en->format('H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center hidden md:table-cell">
                            @php
                                $estadoClases = match($ingreso->socio->estado) {
                                    'activo'     => 'bg-emerald-100 text-emerald-700',
                                    'suspendido' => 'bg-amber-100 text-amber-700',
                                    default      => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $estadoClases }}">
                                {{ \App\Models\Socio::etiquetaEstado($ingreso->socio->estado) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                            Sin ingresos registrados en el período seleccionado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($ingresos->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $ingresos->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
