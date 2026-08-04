@extends('layouts.app')

@section('title', 'Finanzas')

@section('content')

@php
    $meses = [
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
        '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
        '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
        '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
    ];
    $nombreMes = ($meses[$mes] ?? $mes) . ' ' . $anio;
@endphp

{{-- Encabezado + selector --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Finanzas</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $nombreMes }}</p>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <form method="GET" action="{{ route('finanzas.index') }}" class="flex items-center gap-2">
            <select name="periodo" onchange="this.form.submit()"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-700">
                @foreach($periodosDisponibles as $p)
                    @php [$pa, $pm] = explode('-', $p); @endphp
                    <option value="{{ $p }}" {{ $p === $periodo ? 'selected' : '' }}>
                        {{ ($meses[$pm] ?? $pm) . ' ' . $pa }}
                    </option>
                @endforeach
            </select>
        </form>
        <form method="GET" action="{{ route('finanzas.exportar') }}" class="flex items-center gap-2">
            <input type="date" name="desde" value="{{ $anio . '-' . $mes . '-01' }}" required
                class="text-sm border border-slate-300 rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-700">
            <span class="text-slate-400 text-sm">a</span>
            <input type="date" name="hasta" value="{{ now()->format('Y-m-d') }}" required
                class="text-sm border border-slate-300 rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-700">
            <button type="submit"
                class="text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 rounded-lg px-3 py-2 transition-colors shadow-sm">
                Exportar PDF
            </button>
        </form>
    </div>
</div>

{{-- Saldo de caja acumulado --}}
<div class="bg-slate-900 rounded-xl shadow-sm p-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Saldo de caja</p>
        <p class="text-3xl font-bold {{ $saldoActual >= 0 ? 'text-white' : 'text-red-400' }}">
            {{ $saldoActual >= 0 ? '' : '−' }}${{ number_format(abs($saldoActual), 2, ',', '.') }}
        </p>
        <p class="text-xs text-slate-400 mt-1">
            @if($apertura)
                Acumulado desde el {{ $apertura->fecha->format('d/m/Y') }} (saldo inicial ${{ number_format($apertura->monto, 2, ',', '.') }})
            @else
                Sin saldo inicial configurado — se acumula desde el primer movimiento registrado.
            @endif
        </p>
    </div>
    <details class="shrink-0">
        <summary class="cursor-pointer text-sm text-slate-200 hover:text-white font-medium select-none list-none px-3 py-2 border border-slate-600 rounded-lg text-center">
            {{ $apertura ? 'Corregir saldo inicial' : 'Cargar saldo inicial' }}
        </summary>
        <form method="POST" action="{{ route('finanzas.apertura.store') }}" class="mt-3 space-y-2 bg-slate-800 rounded-lg p-4 w-full sm:w-80">
            @csrf
            <input type="hidden" name="periodo" value="{{ $periodo }}">
            <div>
                <label class="block text-xs text-slate-300 mb-1">Monto</label>
                <input type="number" name="monto" min="0" step="0.01" required
                    value="{{ old('monto', $apertura->monto ?? '') }}"
                    class="w-full px-3 py-2 text-sm rounded-lg border-0 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1">Fecha</label>
                <input type="date" name="fecha" required
                    value="{{ old('fecha', optional($apertura?->fecha)->format('Y-m-d')) }}"
                    class="w-full px-3 py-2 text-sm rounded-lg border-0 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1">Descripción (opcional)</label>
                <input type="text" name="descripcion" maxlength="255"
                    value="{{ old('descripcion', $apertura->descripcion ?? '') }}"
                    class="w-full px-3 py-2 text-sm rounded-lg border-0 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                Guardar
            </button>
        </form>
    </details>
</div>

{{-- Tarjetas resumen --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Ingresos</p>
        <p class="text-3xl font-bold text-green-600">${{ number_format($totalIngresos, 2, ',', '.') }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $pagos->count() }} {{ $pagos->count() === 1 ? 'pago' : 'pagos' }} registrados</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Egresos</p>
        <p class="text-3xl font-bold text-red-600">${{ number_format($totalEgresos, 2, ',', '.') }}</p>
        <p class="text-xs text-slate-400 mt-1">Gastos del período (incluye sueldos pagados)</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Balance</p>
        <p class="text-3xl font-bold {{ $balance >= 0 ? 'text-slate-900' : 'text-red-600' }}">
            {{ $balance >= 0 ? '' : '−' }}${{ number_format(abs($balance), 2, ',', '.') }}
        </p>
        <p class="text-xs {{ $balance >= 0 ? 'text-green-600' : 'text-red-500' }} mt-1 font-medium">
            {{ $balance >= 0 ? 'Superávit' : 'Déficit' }}
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ─── INGRESOS ─── --}}
    <div class="space-y-4">

        {{-- Desglose por método --}}
        @if($pagos->isNotEmpty())
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ingresos por método de pago</h2>
            </div>
            <dl class="divide-y divide-slate-100">
                @foreach($ingresosPorMetodo as $metodo => $datos)
                    <div class="flex items-center justify-between px-5 py-3 text-sm">
                        <dt class="text-slate-600">{{ \App\Models\Pago::etiquetaMetodo($metodo) }}</dt>
                        <dd class="text-right">
                            <span class="font-semibold text-slate-900">${{ number_format($datos['total'], 2, ',', '.') }}</span>
                            <span class="text-xs text-slate-400 ml-1">({{ $datos['cantidad'] }})</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
        @endif

        {{-- Lista de pagos --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Pagos recibidos
                    @if($pagos->count())
                        <span class="ml-1.5 bg-green-100 text-green-700 text-xs font-medium px-1.5 py-0.5 rounded-full">{{ $pagos->count() }}</span>
                    @endif
                </h2>
                <a href="{{ route('cuotas.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">Ver cobros →</a>
            </div>
            @if($pagos->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-slate-400">Sin pagos registrados en este período.</div>
            @else
                <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @foreach($pagos as $pago)
                        <li class="flex items-center justify-between px-5 py-3 gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">
                                    {{ $pago->socio?->nombreCompleto() ?? '—' }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $pago->fecha->format('d/m') }}
                                    &middot; {{ \App\Models\Pago::etiquetaMetodo($pago->metodo_pago) }}
                                    @if($pago->cuotaMensual)
                                        &middot; {{ $pago->cuotaMensual->periodoFormateado() }}
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-semibold text-green-700 shrink-0">${{ number_format($pago->total, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between text-sm font-semibold">
                    <span class="text-slate-600">Total</span>
                    <span class="text-green-700">${{ number_format($totalIngresos, 2, ',', '.') }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ─── EGRESOS ─── --}}
    <div class="space-y-4">

        {{-- Sueldos docentes --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sueldos docentes</h2>
                <a href="{{ route('profesores.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">Ver profesores →</a>
            </div>
            @if($profesoresConSueldo->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-slate-400">No hay profesores activos registrados.</div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($profesoresConSueldo as $profesor)
                        @php
                            $sueldoTotal = $profesor->disciplinas->sum('pivot.sueldo');
                            $pago = $sueldosPagados->get($profesor->id);
                        @endphp
                        <li class="px-5 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('profesores.show', $profesor) }}" class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                        {{ $profesor->nombreCompleto() }}
                                    </a>
                                    @if($profesor->disciplinas->count() > 1)
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $profesor->disciplinas->map(fn($d) => $d->nombre . ' $' . number_format($d->pivot->sueldo, 0, ',', '.'))->join(' · ') }}
                                        </p>
                                    @else
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $profesor->disciplinas->first()?->nombre ?? '' }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-semibold text-red-600 block">${{ number_format($sueldoTotal, 2, ',', '.') }}</span>
                                    @if($pago)
                                        <div class="flex items-center gap-1.5 justify-end mt-1">
                                            <span class="text-xs bg-green-100 text-green-700 font-medium px-1.5 py-0.5 rounded-full">Pagado {{ $pago->fecha->format('d/m') }}</span>
                                            <form method="POST" action="{{ route('finanzas.egresos.destroy', $pago) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors"
                                                    onclick="return confirm('¿Deshacer el pago de este sueldo?')" title="Deshacer pago">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($sueldoTotal > 0)
                                        <form method="POST" action="{{ route('finanzas.sueldos.pagar', $profesor) }}" class="mt-1">
                                            @csrf
                                            <input type="hidden" name="periodo" value="{{ $periodo }}">
                                            <button type="submit"
                                                class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors"
                                                onclick="return confirm('¿Registrar el pago del sueldo de {{ $profesor->nombreCompleto() }} por ${{ number_format($sueldoTotal, 2, ',', '.') }}?')">
                                                Pagar sueldo
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between text-sm font-semibold">
                    <span class="text-slate-600">Total sueldos configurados</span>
                    <span class="text-red-600">${{ number_format($totalSueldos, 2, ',', '.') }}</span>
                </div>
            @endif
        </div>

        {{-- Gastos adicionales --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Gastos adicionales
                    @if($egresos->count())
                        <span class="ml-1.5 bg-red-100 text-red-700 text-xs font-medium px-1.5 py-0.5 rounded-full">{{ $egresos->count() }}</span>
                    @endif
                </h2>
            </div>

            {{-- Formulario agregar gasto --}}
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <form method="POST" action="{{ route('finanzas.egresos.store') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <input type="text" name="descripcion" placeholder="Descripción del gasto…"
                                value="{{ old('descripcion') }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400
                                    {{ $errors->has('descripcion') ? 'border-red-400' : '' }}">
                            @error('descripcion')<p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <select name="categoria"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                @foreach(\App\Models\Egreso::categorias() as $val => $label)
                                    <option value="{{ $val }}" {{ old('categoria') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex rounded-lg border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-r border-slate-300 select-none">$</span>
                            <input type="number" name="monto" min="0.01" step="0.01" placeholder="0,00"
                                value="{{ old('monto') }}"
                                class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none text-right w-0">
                        </div>
                        <div>
                            <input type="date" name="fecha"
                                value="{{ old('fecha', $anio . '-' . $mes . '-01') }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <button type="submit"
                                class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                                Registrar gasto
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Lista --}}
            @if($egresos->isEmpty())
                <div class="px-5 py-6 text-center text-sm text-slate-400">Sin gastos adicionales registrados.</div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($egresos as $egreso)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900">{{ $egreso->descripcion }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $egreso->fecha->format('d/m') }}
                                    &middot; {{ \App\Models\Egreso::etiquetaCategoria($egreso->categoria) }}
                                    @if($egreso->observaciones)
                                        &middot; {{ $egreso->observaciones }}
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-semibold text-red-600 shrink-0">${{ number_format($egreso->monto, 2, ',', '.') }}</span>
                            <form method="POST" action="{{ route('finanzas.egresos.destroy', $egreso) }}" class="shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-slate-300 hover:text-red-500 transition-colors p-1"
                                    onclick="return confirm('¿Eliminar este gasto?')">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between text-sm font-semibold">
                    <span class="text-slate-600">Total gastos</span>
                    <span class="text-red-600">${{ number_format($egresos->sum('monto'), 2, ',', '.') }}</span>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
