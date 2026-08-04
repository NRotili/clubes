@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Encabezado --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Resumen general</h1>
        <p class="text-sm text-slate-500 mt-1">{{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}</p>
    </div>

    {{-- Tarjetas de métricas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Socios activos</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalActivos }}</p>
            @if($nuevosMes > 0)
                <p class="text-xs text-emerald-600 mt-1 font-medium">+{{ $nuevosMes }} este mes</p>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Recaudación del mes</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">${{ number_format($recaudacionMes, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ now()->isoFormat('MMMM') }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Cuotas impagas</p>
            <p class="text-3xl font-bold {{ $cantImpagas > 0 ? 'text-red-600' : 'text-slate-900' }} mt-2">{{ $cantImpagas }}</p>
            @if($deudaTotal > 0)
                <p class="text-xs text-red-500 mt-1 font-medium">${{ number_format($deudaTotal, 0, ',', '.') }} en deuda</p>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Ingresos hoy</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $ingresosHoy }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $ingresosMes }} en el mes</p>
        </div>

    </div>

    {{-- Segunda fila: estados + asistencia 14 días --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Distribución de socios --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">Estado de socios</h2>
            <div class="space-y-3">
                @php $total = $totalActivos + $totalInactivos + $totalSuspendidos ?: 1; @endphp

                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-emerald-700">Activos</span>
                        <span class="text-slate-500">{{ $totalActivos }}</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ round($totalActivos / $total * 100) }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-slate-600">Inactivos</span>
                        <span class="text-slate-500">{{ $totalInactivos }}</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-400 rounded-full" style="width: {{ round($totalInactivos / $total * 100) }}%"></div>
                    </div>
                </div>

                @if($totalSuspendidos > 0)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-amber-700">Suspendidos</span>
                        <span class="text-slate-500">{{ $totalSuspendidos }}</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-400 rounded-full" style="width: {{ round($totalSuspendidos / $total * 100) }}%"></div>
                    </div>
                </div>
                @endif

                <p class="text-xs text-slate-400 pt-1">{{ $total }} socios registrados · {{ $cuotasMes }} con cuota este mes</p>
            </div>
        </div>

        {{-- Asistencia últimos 14 días --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-700">Asistencia — últimos 14 días</h2>
                <a href="{{ route('asistencia.index') }}" class="text-xs text-blue-600 hover:underline">Ver completo →</a>
            </div>
            @php $maxDia = $ingresosPorDia->max('total') ?: 1; @endphp
            <div class="flex items-end gap-1 h-20">
                @foreach($ingresosPorDia as $dia)
                    <div class="flex-1 flex flex-col items-center gap-1 group">
                        <div class="w-full bg-blue-100 hover:bg-blue-400 rounded-t transition-colors relative"
                             style="height: {{ max(4, round($dia['total'] / $maxDia * 64)) }}px"
                             title="{{ $dia['total'] }} ingresos el {{ $dia['fecha'] }}">
                            @if($dia['total'] > 0)
                                <span class="absolute -top-5 left-1/2 -translate-x-1/2 text-xs text-slate-500 opacity-0 group-hover:opacity-100 whitespace-nowrap">
                                    {{ $dia['total'] }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[9px] text-slate-400 rotate-0">{{ $dia['fecha'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Tercera fila: últimos ingresos + top deudores --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Últimos ingresos --}}
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Últimos ingresos por QR</h2>
                <a href="{{ route('asistencia.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos →</a>
            </div>
            @forelse($ultimosIngresos as $ingreso)
                <div class="flex items-center gap-3 px-5 py-3 border-b border-slate-50 last:border-0">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 shrink-0">
                        {{ $ingreso->socio ? strtoupper(substr($ingreso->socio->nombre, 0, 1) . substr($ingreso->socio->apellido, 0, 1)) : '?' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $ingreso->socio?->nombreCompleto() ?? 'Socio eliminado' }}</p>
                        <p class="text-xs text-slate-400">{{ $ingreso->socio ? 'N° '.$ingreso->socio->numero_socio : '' }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs font-semibold text-slate-700">{{ $ingreso->ingresado_en->format('H:i') }}</p>
                        <p class="text-xs text-slate-400">{{ $ingreso->ingresado_en->format('d/m') }}</p>
                    </div>
                </div>
            @empty
                <p class="px-5 py-8 text-sm text-slate-400 text-center">Sin ingresos registrados aún.</p>
            @endforelse
        </div>

        {{-- Top deudores --}}
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Principales deudores</h2>
                <a href="{{ route('deudores.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos →</a>
            </div>
            @forelse($topDeudores as $item)
                <div class="flex items-center gap-3 px-5 py-3 border-b border-slate-50 last:border-0">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold text-red-400 shrink-0">
                        {{ $item['socio'] ? strtoupper(substr($item['socio']->nombre, 0, 1) . substr($item['socio']->apellido, 0, 1)) : '?' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        @if($item['socio'])
                            <a href="{{ route('socios.show', $item['socio']) }}" class="text-sm font-medium text-slate-800 hover:text-blue-600 truncate block">
                                {{ $item['socio']->nombreCompleto() }}
                            </a>
                        @else
                            <span class="text-sm font-medium text-slate-800 truncate block">Socio eliminado</span>
                        @endif
                        <p class="text-xs text-slate-400">{{ $item['cant'] }} {{ $item['cant'] === 1 ? 'cuota' : 'cuotas' }} impagas</p>
                    </div>
                    <span class="text-sm font-bold text-red-600 shrink-0">${{ number_format($item['deuda'], 0, ',', '.') }}</span>
                </div>
            @empty
                <p class="px-5 py-8 text-sm text-slate-400 text-center">Sin deudores. ¡Todo al día!</p>
            @endforelse
        </div>

    </div>

</div>
@endsection
