@extends('layouts.app')

@section('title', 'Comandos Artisan')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <span class="text-slate-400">Configuración</span>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Comandos Artisan</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Comandos Artisan</h1>
    <p class="text-sm text-slate-500 mt-0.5">Ejecutá tareas de mantenimiento del servidor sin acceder a la consola.</p>
</div>

@if(session('artisan_output') !== null)
    <div class="mb-6 bg-slate-900 border border-slate-700 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-700 bg-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-200">Salida: {{ session('artisan_label') }}</h2>
        </div>
        <pre class="p-5 text-xs text-emerald-400 overflow-x-auto whitespace-pre-wrap">{{ session('artisan_output') !== '' ? session('artisan_output') : '(sin salida)' }}</pre>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($comandos as $clave => $comando)
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-slate-700">{{ $comando['label'] }}</h2>
                @if($comando['peligroso'])
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-700 shrink-0">
                        Riesgoso
                    </span>
                @endif
            </div>
            <div class="p-5 flex flex-col flex-1 justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">{{ $comando['descripcion'] }}</p>
                    <code class="mt-2 inline-block text-xs text-slate-400 bg-slate-50 px-2 py-1 rounded">
                        php artisan {{ $comando['signature'] }}{{ isset($comando['params']) ? ' ' . implode(' ', array_keys($comando['params'])) : '' }}
                    </code>
                </div>
                <form method="POST" action="{{ route('artisan.run') }}"
                    @if($comando['peligroso'])
                        onsubmit="return confirm('¿Seguro que querés ejecutar &quot;{{ addslashes($comando['label']) }}&quot;? Esta acción puede ser irreversible.')"
                    @endif
                >
                    @csrf
                    <input type="hidden" name="comando" value="{{ $clave }}">
                    <button type="submit"
                        class="inline-flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm
                            {{ $comando['peligroso']
                                ? 'bg-red-600 hover:bg-red-700 text-white'
                                : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        Ejecutar
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

@endsection
