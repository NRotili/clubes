@extends('layouts.app')
@section('title', 'Deudores')

@section('content')
<div class="space-y-5">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Deudores</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ $totalDeudores }} {{ $totalDeudores === 1 ? 'socio' : 'socios' }} con cuotas impagas ·
                <span class="font-semibold text-red-600">${{ number_format($totalDeuda, 2, ',', '.') }}</span> en deuda total
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Estado del socio</label>
            <select name="estado" class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="activo"   @selected($estado === 'activo')>Activos</option>
                <option value="todos"    @selected($estado === 'todos')>Todos los estados</option>
                <option value="inactivo" @selected($estado === 'inactivo')>Inactivos</option>
                <option value="suspendido" @selected($estado === 'suspendido')>Suspendidos</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Ordenar por</label>
            <select name="ordenar" class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="deuda"    @selected($ordenar === 'deuda')>Mayor deuda</option>
                <option value="cantidad" @selected($ordenar === 'cantidad')>Más cuotas</option>
                <option value="nombre"   @selected($ordenar === 'nombre')>Apellido</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            Filtrar
        </button>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Socio</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Estado</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Cuotas</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Períodos</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Deuda</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($deudores as $item)
                    @php
                        $socio = $item['socio'];
                        $estadoClases = [
                            'activo'     => 'bg-emerald-100 text-emerald-700',
                            'inactivo'   => 'bg-slate-100 text-slate-600',
                            'suspendido' => 'bg-amber-100 text-amber-700',
                        ][$socio->estado] ?? 'bg-slate-100 text-slate-600';
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold text-red-400 shrink-0">
                                    {{ strtoupper(substr($socio->nombre, 0, 1) . substr($socio->apellido, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $socio->nombreCompleto() }}</p>
                                    <p class="text-xs text-slate-400">N° {{ $socio->numero_socio }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center hidden sm:table-cell">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $estadoClases }}">
                                {{ \App\Models\Socio::etiquetaEstado($socio->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold text-slate-700">{{ $item['cantidad'] }}</span>
                            @if($item['vencidas'] > 0)
                                <span class="ml-1 text-xs text-red-500">({{ $item['vencidas'] }} venc.)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <div class="flex flex-wrap gap-1">
                                @foreach($item['periodos'] as $p)
                                    <span class="text-xs bg-red-50 text-red-600 px-1.5 py-0.5 rounded font-mono">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $p)->isoFormat('MMM YY') }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold text-red-600">${{ number_format($item['deuda'], 2, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('socios.show', $socio) }}"
                               class="text-xs text-blue-600 hover:underline font-medium">Ver →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            No hay deudores con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
