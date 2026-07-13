@props(['titulo', 'icono' => null, 'abierta' => false])

<details {{ $abierta ? 'open' : '' }} class="group bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none list-none">
        <span class="flex items-center gap-3 text-base font-semibold text-slate-900">
            @if($icono)
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icono }}"/>
                </svg>
            @endif
            {{ $titulo }}
        </span>
        <svg class="w-4 h-4 text-slate-400 transition-transform group-open:rotate-180 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
        </svg>
    </summary>
    <div class="px-5 pb-5 pt-1 border-t border-slate-100 text-sm text-slate-600 space-y-3">
        {{ $slot }}
    </div>
</details>
