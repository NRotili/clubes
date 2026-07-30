@extends('layouts.app')

@section('title', 'Registrar Pago')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('cuotas.index') }}" class="hover:text-slate-700 transition-colors">Cuotas</a>
    @if($cuota)
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
        </svg>
        <a href="{{ route('cuotas.show', $cuota) }}" class="hover:text-slate-700 transition-colors">
            {{ $cuota->socio->nombreCompleto() }} — {{ $cuota->periodoFormateado() }}
        </a>
    @endif
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Registrar pago</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Registrar pago</h1>
    @if($cuota)
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $cuota->socio->nombreCompleto() }} &middot; {{ $cuota->periodoFormateado() }}
            &middot; Saldo pendiente: <span class="font-semibold text-red-600">${{ number_format($cuota->saldo(), 2, ',', '.') }}</span>
        </p>
    @endif
</div>

@if($tieneFamiliar)
    <div class="mb-6 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3.5">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
        </svg>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-amber-800">Este socio tiene un grupo familiar</p>
            <p class="text-sm text-amber-700 mt-0.5">Podés registrar el pago de todos los integrantes en un solo paso.</p>
        </div>
        <a href="{{ route('pagos.create-familiar', ['socio_id' => $cuota->socio_id, 'periodo' => $cuota->periodo]) }}"
            class="shrink-0 inline-flex items-center gap-1.5 text-sm font-medium text-amber-800 bg-amber-100 hover:bg-amber-200 border border-amber-300 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
            Pagar grupo familiar
        </a>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
        </svg>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('pagos.store') }}" novalidate>
    @csrf

    @if($cuota)
        <input type="hidden" name="cuota_mensual_id" value="{{ $cuota->id }}">
        <input type="hidden" name="socio_id" value="{{ $cuota->socio_id }}">
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Datos del pago --}}
        <div class="lg:col-span-1 space-y-6">
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
                            Método de pago <span class="text-red-500">*</span>
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
                            placeholder="Número de comprobante, referencia…">{{ old('observaciones') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Total calculado --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-baseline">
                    <span class="text-sm font-semibold text-slate-700">Total a cobrar</span>
                    <span id="total-display" class="text-2xl font-bold text-slate-900">$0,00</span>
                </div>
            </div>
        </div>

        {{-- Ítems --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Ítems del pago</h2>
                    <button type="button" id="btn-agregar-item"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar ítem
                    </button>
                </div>

                <div id="items-container" class="divide-y divide-slate-100">
                    @php
                        $itemsExcluidos = 0;
                        $itemsIniciales = old('items');
                        if (!$itemsIniciales && $cuota) {
                            $itemsIniciales = collect($cuota->items)
                                ->reject(fn($item) => ($item['beca'] ?? false) || (float) ($item['monto'] ?? 0) <= 0)
                                ->values()
                                ->all();
                            $itemsExcluidos = count($cuota->items) - count($itemsIniciales);
                        }
                        if (empty($itemsIniciales)) {
                            $itemsIniciales = [['descripcion' => '', 'monto' => '']];
                        }
                    @endphp

                    @if($itemsExcluidos > 0)
                        <div class="px-4 pt-4 text-xs text-slate-500">
                            Se {{ $itemsExcluidos === 1 ? 'excluyó 1 ítem becado' : "excluyeron {$itemsExcluidos} ítems becados" }} ($0) de este pago.
                        </div>
                    @endif

                    @foreach($itemsIniciales as $i => $item)
                        <div class="item-fila flex items-center gap-3 p-4">
                            <input type="text" name="items[{{ $i }}][descripcion]"
                                value="{{ $item['descripcion'] ?? '' }}"
                                placeholder="Descripción del ítem"
                                class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
                            <div class="flex rounded-lg border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 w-36 shrink-0">
                                <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-r border-slate-300 select-none">$</span>
                                <input type="number" name="items[{{ $i }}][monto]"
                                    value="{{ $item['monto'] ?? '' }}"
                                    min="0" step="0.01"
                                    placeholder="0,00"
                                    class="monto-input flex-1 px-3 py-2 text-sm bg-white focus:outline-none text-right w-0">
                            </div>
                            <button type="button" class="btn-quitar-item text-slate-400 hover:text-red-500 transition-colors p-1 shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <div class="flex items-center justify-end gap-3 mt-6">
        @if($cuota)
            <a href="{{ route('cuotas.show', $cuota) }}"
                class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
        @else
            <a href="{{ route('cuotas.index') }}"
                class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
        @endif
        <button type="submit"
            class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-sm">
            Registrar pago
        </button>
    </div>
</form>

<script>
(function () {
    const container  = document.getElementById('items-container');
    const btnAgregar = document.getElementById('btn-agregar-item');
    const totalEl    = document.getElementById('total-display');

    function indice() {
        return container.querySelectorAll('.item-fila').length;
    }

    function actualizarTotal() {
        let suma = 0;
        container.querySelectorAll('.monto-input').forEach(inp => {
            suma += parseFloat(inp.value) || 0;
        });
        totalEl.textContent = '$' + suma.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function crearFila(idx) {
        const fila = document.createElement('div');
        fila.className = 'item-fila flex items-center gap-3 p-4 border-t border-slate-100';

        fila.innerHTML = `
            <input type="text" name="items[${idx}][descripcion]"
                placeholder="Descripción del ítem"
                class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
            <div class="flex rounded-lg border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 w-36 shrink-0">
                <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-r border-slate-300 select-none">$</span>
                <input type="number" name="items[${idx}][monto]"
                    min="0" step="0.01" placeholder="0,00"
                    class="monto-input flex-1 px-3 py-2 text-sm bg-white focus:outline-none text-right w-0">
            </div>
            <button type="button" class="btn-quitar-item text-slate-400 hover:text-red-500 transition-colors p-1 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>`;

        fila.querySelector('.monto-input').addEventListener('input', actualizarTotal);
        fila.querySelector('.btn-quitar-item').addEventListener('click', () => {
            fila.remove();
            actualizarTotal();
        });

        return fila;
    }

    btnAgregar.addEventListener('click', () => {
        container.appendChild(crearFila(indice()));
    });

    container.addEventListener('input', e => {
        if (e.target.classList.contains('monto-input')) actualizarTotal();
    });

    container.addEventListener('click', e => {
        const btn = e.target.closest('.btn-quitar-item');
        if (btn) {
            btn.closest('.item-fila').remove();
            actualizarTotal();
        }
    });

    actualizarTotal();
})();
</script>

@endsection
