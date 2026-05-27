@extends('layouts.app')

@section('title', 'Escáner QR')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Escáner QR</h1>
        <p class="text-sm text-slate-500 mt-0.5">Registrá el ingreso de socios escaneando su carnet.</p>
    </div>
    <button id="btn-fullscreen"
        class="hidden lg:inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 border border-slate-300 hover:border-slate-400 rounded-lg px-3 py-2 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
        </svg>
        Pantalla completa
    </button>
</div>

{{-- ── Tabs modo ───────────────────────────────────────────────────────────── --}}
<div class="flex gap-1 bg-slate-100 rounded-xl p-1 w-fit mb-6">
    <button id="tab-camara"
        class="tab-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-white text-slate-900 shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/>
        </svg>
        Cámara
    </button>
    <button id="tab-lector"
        class="tab-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors text-slate-500 hover:text-slate-700">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5ZM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5Z"/>
        </svg>
        Lector físico
    </button>
</div>

<div class="grid lg:grid-cols-2 gap-6 items-start">

    {{-- ════════════════════════════════════════════════════════════════════════
         PANEL IZQUIERDO — cambia según el modo activo
    ════════════════════════════════════════════════════════════════════════ --}}

    {{-- ── Modo cámara ── --}}
    <div id="panel-camara" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span id="status-dot" class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                <span id="status-label" class="text-sm font-medium text-slate-500">Cámara detenida</span>
            </div>
            <div class="flex gap-2">
                <button id="btn-start"
                    class="inline-flex items-center gap-1.5 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/>
                    </svg>
                    Iniciar
                </button>
                <button id="btn-stop"
                    class="hidden inline-flex items-center gap-1.5 text-sm font-medium bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg px-3 py-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z"/>
                    </svg>
                    Detener
                </button>
            </div>
        </div>

        <div class="relative bg-black aspect-square w-full">
            <div id="qr-reader" class="w-full h-full"></div>

            {{-- Mira --}}
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                <div class="relative w-56 h-56">
                    <span class="absolute top-0 left-0 w-10 h-10 border-t-4 border-l-4 border-white rounded-tl-lg"></span>
                    <span class="absolute top-0 right-0 w-10 h-10 border-t-4 border-r-4 border-white rounded-tr-lg"></span>
                    <span class="absolute bottom-0 left-0 w-10 h-10 border-b-4 border-l-4 border-white rounded-bl-lg"></span>
                    <span class="absolute bottom-0 right-0 w-10 h-10 border-b-4 border-r-4 border-white rounded-br-lg"></span>
                    <div id="scan-line" class="hidden absolute top-0 left-2 right-2 h-0.5 bg-blue-400 rounded-full opacity-80 animate-scan"></div>
                </div>
            </div>

            <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-slate-400">
                <svg class="w-16 h-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/>
                </svg>
                <p class="text-sm font-medium opacity-60">Presioná "Iniciar" para activar la cámara</p>
            </div>
        </div>
    </div>

    {{-- ── Modo lector físico ── --}}
    <div id="panel-lector" class="hidden bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center gap-2">
            <span id="lector-dot" class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
            <span class="text-sm font-medium text-green-600">Listo para escanear</span>
        </div>

        <div class="p-8 flex flex-col items-center gap-6">
            {{-- Ícono lector --}}
            <div class="w-24 h-24 rounded-2xl bg-slate-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5ZM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5Z"/>
                </svg>
            </div>

            <div class="text-center">
                <p class="font-semibold text-slate-700">Apuntá el lector al código QR del carnet</p>
                <p class="text-sm text-slate-400 mt-1">Compatible con lectores USB, Bluetooth y WiFi (modo teclado HID)</p>
            </div>

            {{-- Campo de captura --}}
            <div class="w-full">
                <label for="lector-input" class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                    Campo de captura
                </label>
                <div class="relative">
                    <input id="lector-input" type="text" autocomplete="off" autocorrect="off" spellcheck="false"
                        placeholder="El lector escribe aquí automáticamente…"
                        class="w-full rounded-xl border-2 border-blue-300 bg-blue-50 focus:border-blue-500 focus:bg-white px-4 py-3 text-slate-700 placeholder-slate-400 text-sm outline-none transition-colors font-mono">
                    <button id="btn-limpiar"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors hidden">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-2">También podés pegar o escribir el UUID manualmente y presionar <kbd class="px-1.5 py-0.5 bg-slate-200 rounded text-slate-600 font-mono text-xs">Enter</kbd></p>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════
         PANEL DERECHO — resultados (compartido por ambos modos)
    ════════════════════════════════════════════════════════════════════════ --}}
    <div>
        {{-- Estado inicial --}}
        <div id="result-idle" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex flex-col items-center justify-center text-center gap-4 min-h-[280px]">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5ZM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5Z"/>
                </svg>
            </div>
            <p class="text-sm text-slate-400">Esperando escaneo…</p>
        </div>

        {{-- Procesando --}}
        <div id="result-loading" class="hidden bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex flex-col items-center justify-center text-center gap-4 min-h-[280px]">
            <div class="w-12 h-12 rounded-full border-4 border-blue-200 border-t-blue-600 animate-spin"></div>
            <p class="text-sm text-slate-500">Verificando…</p>
        </div>

        {{-- OK --}}
        <div id="result-ok" class="hidden bg-white rounded-2xl shadow-sm border-2 border-green-400 overflow-hidden min-h-[280px]">
            <div class="bg-green-500 px-5 py-3 flex items-center gap-3">
                <svg class="w-6 h-6 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <div>
                    <p class="text-white font-bold text-base">Ingreso registrado</p>
                    <p class="text-green-100 text-xs" id="ok-hora"></p>
                </div>
            </div>
            <div id="result-card" class="p-5"></div>
        </div>

        {{-- Duplicado --}}
        <div id="result-duplicado" class="hidden bg-white rounded-2xl shadow-sm border-2 border-amber-400 overflow-hidden min-h-[280px]">
            <div class="bg-amber-400 px-5 py-3 flex items-center gap-3">
                <svg class="w-6 h-6 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z"/>
                </svg>
                <div>
                    <p class="text-white font-bold text-base">Ya registrado</p>
                    <p class="text-amber-100 text-xs" id="dup-hora"></p>
                </div>
            </div>
            <div id="result-card-dup" class="p-5"></div>
        </div>

        {{-- Inactivo --}}
        <div id="result-inactivo" class="hidden bg-white rounded-2xl shadow-sm border-2 border-red-400 overflow-hidden min-h-[280px]">
            <div class="bg-red-500 px-5 py-3 flex items-center gap-3">
                <svg class="w-6 h-6 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <div>
                    <p class="text-white font-bold text-base" id="inactivo-titulo">Acceso denegado</p>
                    <p class="text-red-100 text-xs" id="inactivo-mensaje"></p>
                </div>
            </div>
            <div id="result-card-inactivo" class="p-5"></div>
        </div>

        {{-- Desconocido --}}
        <div id="result-desconocido" class="hidden bg-white rounded-2xl shadow-sm border-2 border-slate-300 overflow-hidden min-h-[280px]">
            <div class="bg-slate-600 px-5 py-3 flex items-center gap-3">
                <svg class="w-6 h-6 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/>
                </svg>
                <div>
                    <p class="text-white font-bold text-base">QR no reconocido</p>
                    <p class="text-slate-300 text-xs">El código no pertenece a ningún socio</p>
                </div>
            </div>
            <div class="p-5 text-center">
                <p class="text-sm text-slate-500">Verificá que el QR sea el carnet digital de un socio registrado en este club.</p>
            </div>
        </div>

        {{-- Historial --}}
        <div class="mt-4 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-700">Últimos escaneos</p>
                <span id="historial-count" class="text-xs text-slate-400 hidden"></span>
            </div>
            <ul id="historial" class="divide-y divide-slate-100 text-sm max-h-52 overflow-y-auto">
                <li class="px-5 py-3 text-slate-400 text-center text-xs" id="historial-vacio">Sin registros aún</li>
            </ul>
        </div>
    </div>
</div>

{{-- html5-qrcode --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
@keyframes scan {
    0%   { top: 0; }
    50%  { top: calc(100% - 2px); }
    100% { top: 0; }
}
.animate-scan { animation: scan 2s linear infinite; }
</style>

<script>
(function () {
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const CHECK_URL = '{{ route("escaner.check") }}';

    let scanner   = null;
    let scanning  = false;
    let cooldown  = false;
    let resetTimer = null;
    let modoActivo = 'camara'; // 'camara' | 'lector'

    const $ = id => document.getElementById(id);

    // ── Tabs ─────────────────────────────────────────────────────────────────
    $('tab-camara').addEventListener('click', () => activarModo('camara'));
    $('tab-lector').addEventListener('click', () => activarModo('lector'));

    function activarModo(modo) {
        modoActivo = modo;

        // Tabs visuales
        const tabActivo   = 'bg-white text-slate-900 shadow-sm';
        const tabInactivo = 'text-slate-500 hover:text-slate-700';

        $('tab-camara').className = 'tab-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors ' +
            (modo === 'camara' ? tabActivo : tabInactivo);
        $('tab-lector').className = 'tab-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors ' +
            (modo === 'lector' ? tabActivo : tabInactivo);

        // Paneles
        $('panel-camara').classList.toggle('hidden', modo !== 'camara');
        $('panel-lector').classList.toggle('hidden', modo !== 'lector');

        // Al cambiar a lector: detener cámara si estaba activa, enfocar input
        if (modo === 'lector') {
            if (scanning) stopScanner();
            setTimeout(() => $('lector-input').focus(), 50);
        }

        showPanel('idle');
        clearTimeout(resetTimer);
        cooldown = false;
    }

    // ── Lector físico ────────────────────────────────────────────────────────
    const lectorInput = $('lector-input');
    const btnLimpiar  = $('btn-limpiar');

    lectorInput.addEventListener('input', () => {
        btnLimpiar.classList.toggle('hidden', !lectorInput.value);
    });

    btnLimpiar.addEventListener('click', () => {
        lectorInput.value = '';
        btnLimpiar.classList.add('hidden');
        lectorInput.focus();
    });

    lectorInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = lectorInput.value.trim();
            if (!val || cooldown) return;
            lectorInput.value = '';
            btnLimpiar.classList.add('hidden');
            procesarCodigo(val);
        }
    });

    // ── Cámara ───────────────────────────────────────────────────────────────
    $('btn-start').addEventListener('click', startScanner);
    $('btn-stop').addEventListener('click', stopScanner);
    $('btn-fullscreen')?.addEventListener('click', () => {
        document.documentElement.requestFullscreen?.();
    });

    function startScanner() {
        if (scanning) return;
        $('btn-start').classList.add('hidden');
        $('btn-stop').classList.remove('hidden');
        $('camera-placeholder').classList.add('hidden');
        $('scan-line').classList.remove('hidden');
        setStatus('active', 'Cámara activa');

        scanner = new Html5Qrcode('qr-reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 220, height: 220 }, aspectRatio: 1 },
            decodedText => {
                if (cooldown) return;
                cooldown = true;
                let uuid = decodedText.trim();
                const m = uuid.match(/\/verificar\/([^\/\?#]+)/);
                if (m) uuid = m[1];
                procesarCodigo(uuid);
            },
            () => {}
        ).catch(() => {
            setStatus('error', 'Sin acceso a la cámara');
            $('btn-start').classList.remove('hidden');
            $('btn-stop').classList.add('hidden');
            $('camera-placeholder').classList.remove('hidden');
            $('scan-line').classList.add('hidden');
            scanning = false;
        });
        scanning = true;
    }

    function stopScanner() {
        if (!scanning) return;
        scanner?.stop().then(() => { scanner.clear(); scanner = null; }).catch(() => {});
        scanning = false;
        $('btn-start').classList.remove('hidden');
        $('btn-stop').classList.add('hidden');
        $('scan-line').classList.add('hidden');
        $('camera-placeholder').classList.remove('hidden');
        setStatus('idle', 'Cámara detenida');
        showPanel('idle');
    }

    // ── Lógica compartida ────────────────────────────────────────────────────
    function procesarCodigo(raw) {
        // Extraer UUID si viene como URL completa
        let uuid = raw;
        const m = raw.match(/\/verificar\/([^\/\?#]+)/);
        if (m) uuid = m[1];

        showPanel('loading');
        clearTimeout(resetTimer);

        fetch(CHECK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ uuid }),
        })
        .then(r => r.json())
        .then(data => {
            addHistorial(data.tipo, data);

            if (data.tipo === 'ok') {
                $('ok-hora').textContent = 'a las ' + data.ingreso_en;
                renderCard('result-card', data.socio, 'green');
                showPanel('ok');
            } else if (data.tipo === 'duplicado') {
                $('dup-hora').textContent = 'registrado a las ' + data.ingreso_en;
                renderCard('result-card-dup', data.socio, 'amber');
                showPanel('duplicado');
            } else if (data.tipo === 'inactivo') {
                $('inactivo-titulo').textContent = data.mensaje;
                $('inactivo-mensaje').textContent = 'Acceso restringido';
                renderCard('result-card-inactivo', data.socio, 'red');
                showPanel('inactivo');
            } else {
                showPanel('desconocido');
            }

            resetTimer = setTimeout(() => {
                showPanel('idle');
                cooldown = false;
                // En modo lector: re-enfocar para el próximo escaneo
                if (modoActivo === 'lector') $('lector-input').focus();
            }, 4000);
        })
        .catch(() => {
            showPanel('idle');
            cooldown = false;
            if (modoActivo === 'lector') $('lector-input').focus();
        });
    }

    // ── Tarjeta socio ────────────────────────────────────────────────────────
    function renderCard(containerId, socio, color) {
        if (!socio) return;
        const badge = { green: 'bg-green-100 text-green-700', amber: 'bg-amber-100 text-amber-700', red: 'bg-red-100 text-red-700' }[color] ?? 'bg-slate-100 text-slate-600';
        const avatar = socio.foto_url
            ? `<img src="${socio.foto_url}" alt="" class="w-14 h-14 rounded-full object-cover border-2 border-slate-200">`
            : `<div class="w-14 h-14 rounded-full bg-slate-200 flex items-center justify-center text-xl font-bold text-slate-600">${socio.iniciales}</div>`;
        $(containerId).innerHTML = `
            <div class="flex items-center gap-4">
                ${avatar}
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 text-lg leading-tight">${socio.nombre}</p>
                    <p class="text-sm text-slate-500">N° ${socio.numero} &middot; ${socio.categoria}</p>
                    <span class="inline-block mt-1 text-xs font-semibold px-2 py-0.5 rounded-full ${badge}">${socio.estado}</span>
                </div>
            </div>`;
    }

    // ── Historial ─────────────────────────────────────────────────────────────
    let totalEscaneos = 0;

    function addHistorial(tipo, data) {
        const ul = $('historial');
        const vacio = $('historial-vacio');
        if (vacio) vacio.remove();

        totalEscaneos++;
        $('historial-count').textContent = totalEscaneos + ' escaneos';
        $('historial-count').classList.remove('hidden');

        const icono = { ok: '✓', duplicado: '⚠', inactivo: '✗', desconocido: '?' }[tipo] ?? '•';
        const clase  = { ok: 'text-green-600', duplicado: 'text-amber-500', inactivo: 'text-red-500', desconocido: 'text-slate-400' }[tipo] ?? 'text-slate-500';
        const label  = { ok: 'Ingreso', duplicado: 'Duplicado', inactivo: 'Denegado', desconocido: 'Desconocido' }[tipo] ?? tipo;
        const now    = new Date().toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const nombre = data.socio?.nombre ?? '—';

        const li = document.createElement('li');
        li.className = 'px-5 py-2.5 flex items-center justify-between gap-3';
        li.innerHTML = `
            <span class="font-medium text-slate-700 truncate">${nombre}</span>
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-xs font-semibold ${clase}">${icono} ${label}</span>
                <span class="text-xs text-slate-400">${now}</span>
            </div>`;
        ul.prepend(li);
        while (ul.children.length > 20) ul.removeChild(ul.lastChild);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    const panels = ['idle', 'loading', 'ok', 'duplicado', 'inactivo', 'desconocido'];
    function showPanel(active) {
        panels.forEach(p => {
            const el = $('result-' + p);
            if (el) el.classList.toggle('hidden', p !== active);
        });
    }

    function setStatus(state, label) {
        const dot = $('status-dot');
        const lbl = $('status-label');
        const clr = { active: 'bg-green-500', error: 'bg-red-500', idle: 'bg-slate-300' }[state] ?? 'bg-slate-300';
        dot.className = `w-2.5 h-2.5 rounded-full ${clr}`;
        lbl.textContent = label;
        lbl.className = state === 'active' ? 'text-sm font-medium text-green-600'
            : state === 'error' ? 'text-sm font-medium text-red-600'
            : 'text-sm font-medium text-slate-500';
    }
})();
</script>
@endsection
