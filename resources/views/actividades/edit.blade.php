@extends('layouts.app')

@section('title', 'Editar — ' . $actividad->nombre)

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('actividades.index') }}" class="hover:text-slate-700 transition-colors">Actividades</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <a href="{{ route('actividades.show', $actividad) }}" class="hover:text-slate-700 transition-colors">{{ $actividad->nombre }}</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Editar</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Editar actividad</h1>
    <p class="text-sm text-slate-500 mt-0.5">{{ $actividad->nombre }}</p>
</div>

@if($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
        </svg>
        <div>
            <p class="font-medium">Por favor corregí los siguientes errores:</p>
            <ul class="list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('actividades.update', $actividad) }}" novalidate>
    @csrf
    @method('PUT')

    @include('actividades._form')

    <div class="flex items-center justify-between pt-2">
        <button type="button"
            onclick="document.getElementById('form-eliminar-actividad').submit()"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
            </svg>
            Eliminar actividad
        </button>
        <div class="flex items-center gap-3">
            <a href="{{ route('actividades.show', $actividad) }}"
                class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                Guardar cambios
            </button>
        </div>
    </div>
</form>

<form id="form-eliminar-actividad" method="POST" action="{{ route('actividades.destroy', $actividad) }}"
    onsubmit="return confirm('¿Eliminar la actividad «{{ addslashes($actividad->nombre) }}»? Se perderán también sus franjas horarias.')">
    @csrf
    @method('DELETE')
</form>

@endsection
