@extends('layouts.app')

@section('title', 'Cuota — ' . $cuota->socio->nombreCompleto())

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('cuotas.index') }}" class="hover:text-slate-700 transition-colors">Cuotas</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">{{ $cuota->socio->nombreCompleto() }} — {{ $cuota->periodoFormateado() }}</span>
</div>

{{-- Encabezado --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-xl font-bold text-slate-900">{{ $cuota->socio->nombreCompleto() }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ \App\Models\CuotaMensual::clasesEstado($cuota->estado) }}">
                    {{ \App\Models\CuotaMensual::etiquetaEstado($cuota->estado) }}
                </span>
            </div>
            <p class="text-sm text-slate-500">
                Período: <span class="font-medium text-slate-700">{{ $cuota->periodoFormateado() }}</span>
                &nbsp;·&nbsp; N° {{ $cuota->socio->numero_socio }}
            </p>
        </div>

        @if($cuota->estado !== 'pagado')
            <a href="{{ route('pagos.create', ['cuota_id' => $cuota->id]) }}"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Registrar pago
            </a>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Detalle de la cuota --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Detalle de la cuota</h2>
        </div>
        <ul class="divide-y divide-slate-100">
            @foreach($cuota->items as $i => $item)
                @if(($item['tipo'] ?? '') === 'por_clase' && $cuota->estado !== 'pagado')
                    <li class="px-5 py-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-700 font-medium">{{ explode(' · ', $item['descripcion'])[0] }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    ${{ number_format($item['costo_clase'], 2, ',', '.') }} por clase
                                    @if($item['clases'] != $item['clases_programadas'])
                                        &middot; <span class="text-amber-600">{{ $item['clases_programadas'] }} programadas</span>
                                    @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('cuotas.ajustar-clases', $cuota) }}" class="flex items-center gap-2 shrink-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="indice" value="{{ $i }}">
                                <div class="flex items-center gap-1.5">
                                    <input type="number" name="clases" value="{{ $item['clases'] }}" min="0"
                                        class="w-16 px-2 py-1 text-sm text-center border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span class="text-xs text-slate-400">clases</span>
                                </div>
                                <span class="text-sm font-medium text-slate-900 w-20 text-right">${{ number_format($item['monto'], 2, ',', '.') }}</span>
                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors">Ajustar</button>
                            </form>
                        </div>
                    </li>
                @elseif($item['beca'] ?? false)
                    <li class="flex justify-between items-center px-5 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-600">{{ $item['descripcion'] }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">Beca</span>
                        </div>
                        <span class="font-medium text-slate-400">$0,00</span>
                    </li>
                @else
                    <li class="flex justify-between px-5 py-3 text-sm">
                        <span class="text-slate-600">{{ $item['descripcion'] }}</span>
                        <span class="font-medium text-slate-900">${{ number_format($item['monto'], 2, ',', '.') }}</span>
                    </li>
                @endif
            @endforeach
        </ul>
        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex justify-between text-sm font-semibold">
            <span class="text-slate-700">Total</span>
            <span class="text-slate-900">${{ number_format($cuota->monto_total, 2, ',', '.') }}</span>
        </div>
    </div>

    {{-- Resumen de cobro --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Resumen de cobro</h2>
            @if($cuota->estaVencida())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
                    Vencida el {{ $cuota->fechaVencimiento()->format('d/m/Y') }}
                </span>
            @else
                <span class="text-xs text-slate-400">Vence el {{ $cuota->fechaVencimiento()->format('d/m/Y') }}</span>
            @endif
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Total a pagar</dt>
                <dd class="font-semibold text-slate-900">${{ number_format($cuota->monto_total, 2, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Pagado</dt>
                <dd class="font-semibold text-green-700">${{ number_format($cuota->monto_pagado, 2, ',', '.') }}</dd>
            </div>
            @if($cuota->estaVencida() && $cuota->recargo() > 0)
                <div class="flex justify-between px-5 py-3 text-sm bg-orange-50">
                    <dt class="text-orange-700">Recargo por mora ({{ \App\Models\ClubConfig::recargoMora() }}%)</dt>
                    <dd class="font-semibold text-orange-700">+${{ number_format($cuota->recargo(), 2, ',', '.') }}</dd>
                </div>
            @endif
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Saldo pendiente</dt>
                <dd class="font-bold text-lg {{ $cuota->saldo() > 0 ? 'text-red-600' : 'text-green-600' }}">
                    ${{ number_format($cuota->saldo(), 2, ',', '.') }}
                </dd>
            </div>
            @if($cuota->estaVencida() && $cuota->recargo() > 0)
                <div class="flex justify-between px-5 py-3 text-sm font-semibold bg-orange-50">
                    <dt class="text-orange-800">Total con recargo</dt>
                    <dd class="text-orange-800">${{ number_format($cuota->saldo() + $cuota->recargo(), 2, ',', '.') }}</dd>
                </div>
            @endif
        </dl>
    </div>
</div>

{{-- Pagos registrados --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Pagos registrados
            @if($cuota->pagos->count())
                <span class="ml-2 bg-slate-200 text-slate-600 text-xs font-medium px-1.5 py-0.5 rounded-full">{{ $cuota->pagos->count() }}</span>
            @endif
        </h2>
    </div>

    @if($cuota->pagos->isEmpty())
        <div class="px-5 py-10 text-center text-sm text-slate-400">
            Sin pagos registrados aún.
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($cuota->pagos as $pago)
                <li class="px-5 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-sm font-semibold text-slate-900">${{ number_format($pago->total, 2, ',', '.') }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ \App\Models\Pago::etiquetaMetodo($pago->metodo_pago) }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $pago->fecha->format('d/m/Y') }}</span>
                            </div>
                            <ul class="space-y-0.5">
                                @foreach($pago->items as $item)
                                    <li class="flex justify-between text-xs text-slate-500">
                                        <span>{{ $item->descripcion }}</span>
                                        <span>${{ number_format($item->monto, 2, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if($pago->observaciones)
                                <p class="text-xs text-slate-400 mt-1.5 italic">{{ $pago->observaciones }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('pagos.destroy', $pago) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-xs text-red-400 hover:text-red-600 transition-colors font-medium"
                                onclick="return confirm('¿Anular este pago? Se recalculará el saldo de la cuota.')">
                                Anular
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

@endsection
