@extends('layouts.app')

@section('title', 'Pagar grupo familiar')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('cuotas.index') }}" class="hover:text-slate-700 transition-colors">Cuotas</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Pago grupo familiar</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Pago grupo familiar</h1>
    <p class="text-sm text-slate-500 mt-0.5">Registrá el pago de todos los integrantes del grupo en un solo paso.</p>
</div>

{{-- Selector de titular y período --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('pagos.create-familiar') }}" class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Socio titular</label>
            <select name="socio_id" onchange="this.form.submit()"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Seleccionar titular…</option>
                @foreach($titulares as $t)
                    <option value="{{ $t->id }}" {{ $titular?->id === $t->id ? 'selected' : '' }}>
                        N° {{ $t->numero_socio }} — {{ $t->nombreCompleto() }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Período</label>
            <input type="month" name="periodo" value="{{ $periodo }}" onchange="this.form.submit()"
                class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </form>
</div>

@if($titular)

@if($cuotasPorSocio->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-10 text-center mb-6">
        <svg class="w-10 h-10 text-green-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
        <p class="text-slate-600 font-medium">Sin cuotas pendientes</p>
        <p class="text-sm text-slate-400 mt-1">El grupo familiar no tiene cuotas pendientes para este período.</p>
    </div>
@else

<form method="POST" action="{{ route('pagos.store-familiar') }}" id="form-familiar">
    @csrf
    <input type="hidden" name="titular_id" value="{{ $titular->id }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Cuotas del grupo --}}
        <div class="lg:col-span-2 space-y-3">
            @foreach($cuotasPorSocio as $entrada)
                @php $cuota = $entrada['cuota']; $socioM = $entrada['socio']; @endphp
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden cuota-card" data-saldo="{{ $cuota->saldo() }}">
                    <div class="px-5 py-4 flex items-start gap-3">
                        <input type="checkbox" name="cuotas[]" value="{{ $cuota->id }}"
                            checked
                            class="cuota-check mt-0.5 w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $socioM->nombreCompleto() }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        N° {{ $socioM->numero_socio }}
                                        @if(!$socioM->esTitular())
                                            &middot; {{ \App\Models\Socio::etiquetaParentesco($socioM->parentesco) }}
                                        @else
                                            &middot; Titular
                                        @endif
                                        &middot; {{ $cuota->periodoFormateado() }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ \App\Models\CuotaMensual::clasesEstado($cuota->estado) }} shrink-0">
                                    {{ \App\Models\CuotaMensual::etiquetaEstado($cuota->estado) }}
                                </span>
                            </div>
                            <ul class="space-y-1">
                                @foreach($cuota->items as $item)
                                    <li class="flex justify-between text-xs text-slate-500">
                                        <span>{{ $item['descripcion'] }}</span>
                                        <span>${{ number_format($item['monto'], 2, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if($cuota->monto_pagado > 0)
                                <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between text-xs text-slate-500">
                                    <span>Ya pagado</span>
                                    <span class="text-green-600">−${{ number_format($cuota->monto_pagado, 2, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs text-slate-400 mb-0.5">Saldo</p>
                            <p class="text-lg font-bold text-red-600">${{ number_format($cuota->saldo(), 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Panel de pago --}}
        <div class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Datos del pago</h2>
                </div>
                <div class="p-5 space-y-4">

                    <div>
                        <label for="fecha" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Fecha <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="metodo_pago" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Método <span class="text-red-500">*</span>
                        </label>
                        <select id="metodo_pago" name="metodo_pago"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Seleccionar…</option>
                            @foreach(\App\Models\Pago::metodos() as $m)
                                <option value="{{ $m }}" {{ old('metodo_pago') === $m ? 'selected' : '' }}>
                                    {{ \App\Models\Pago::etiquetaMetodo($m) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-slate-700 mb-1.5">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="2"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y placeholder-slate-400"
                            placeholder="Referencia, comprobante…">{{ old('observaciones') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Total --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-semibold text-slate-700">Total a cobrar</span>
                    <span id="total-display" class="text-2xl font-bold text-slate-900">$0,00</span>
                </div>
                <p class="text-xs text-slate-400" id="cuotas-seleccionadas">0 cuotas seleccionadas</p>
            </div>

            <button type="submit"
                class="w-full px-6 py-3 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                Registrar pagos del grupo
            </button>
            <a href="{{ route('socios.show', $titular) }}"
                class="block w-full text-center px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
        </div>

    </div>
</form>

@endif {{-- cuotasPorSocio --}}

@endif {{-- titular --}}

<script>
(function () {
    const checks   = document.querySelectorAll('.cuota-check');
    const totalEl  = document.getElementById('total-display');
    const countEl  = document.getElementById('cuotas-seleccionadas');

    function actualizar() {
        let total = 0, count = 0;
        checks.forEach(ch => {
            if (ch.checked) {
                total += parseFloat(ch.closest('.cuota-card').dataset.saldo) || 0;
                count++;
            }
        });
        totalEl.textContent = '$' + total.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        countEl.textContent = count + ' cuota' + (count !== 1 ? 's' : '') + ' seleccionada' + (count !== 1 ? 's' : '');
    }

    checks.forEach(ch => ch.addEventListener('change', actualizar));
    actualizar();
})();
</script>

@endsection
