@extends('layouts.app')

@section('title', 'Cuotas')

@section('content')

@php
    $periodoActual = now()->format('Y-m');
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Cuotas mensuales</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $cuotas->total() }} registros encontrados</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        {{-- Generar cuotas --}}
        <form method="POST" action="{{ route('cuotas.generar') }}" class="flex items-center gap-2">
        @csrf
        <input type="month" name="periodo" value="{{ $periodoActual }}"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm whitespace-nowrap"
            onclick="return confirm('¿Generar cuotas para el período seleccionado? Solo se crearán las que no existan.')">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3"/>
            </svg>
            Generar cuotas
        </button>
    </form>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('cuotas.index') }}" class="bg-white border border-slate-200 rounded-xl p-4 mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar socio…"
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
        </div>
        <select name="periodo"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-700">
            <option value="">Todos los períodos</option>
            @foreach($periodos as $p)
                <option value="{{ $p }}" {{ request('periodo') === $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
        <select name="estado"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-700">
            <option value="">Todos los estados</option>
            <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="parcial"   {{ request('estado') === 'parcial'   ? 'selected' : '' }}>Pago parcial</option>
            <option value="pagado"    {{ request('estado') === 'pagado'    ? 'selected' : '' }}>Pagado</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">Filtrar</button>
            @if(request()->hasAny(['buscar', 'periodo', 'estado']))
                <a href="{{ route('cuotas.index') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 text-sm font-medium border border-slate-300 rounded-lg transition-colors">Limpiar</a>
            @endif
        </div>
    </div>
</form>

@if($cuotas->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <p class="text-slate-500">No hay cuotas generadas para los filtros seleccionados.</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Socio</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Período</th>
                        <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Total</th>
                        <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Pagado</th>
                        <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Saldo</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Estado</th>
                        <th class="px-4 py-3 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($cuotas as $cuota)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('socios.show', $cuota->socio) }}" class="font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                    {{ $cuota->socio->nombreCompleto() }}
                                </a>
                                <p class="text-xs text-slate-400 font-mono">N° {{ $cuota->socio->numero_socio }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $cuota->periodoFormateado() }}</td>
                            <td class="px-4 py-3 text-right font-medium text-slate-900">${{ number_format($cuota->monto_total, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-slate-600">${{ number_format($cuota->monto_pagado, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $cuota->saldo() > 0 ? 'text-red-600' : 'text-green-600' }}">
                                ${{ number_format($cuota->saldo(), 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ \App\Models\CuotaMensual::clasesEstado($cuota->estado) }}">
                                        {{ \App\Models\CuotaMensual::etiquetaEstado($cuota->estado) }}
                                    </span>
                                    @if($cuota->estaVencida())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-orange-50 text-orange-700 border-orange-200">
                                            Vencida
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('cuotas.show', $cuota) }}" title="Ver"
                                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </a>
                                    @if($cuota->estado !== 'pagado')
                                        <a href="{{ route('pagos.create', ['cuota_id' => $cuota->id]) }}" title="Registrar pago"
                                            class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/>
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
        @if($cuotas->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
                {{ $cuotas->links() }}
            </div>
        @endif
    </div>
@endif

@endsection
