@extends('layouts.app')

@section('title', 'Nuevo Socio')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('socios.index') }}" class="hover:text-slate-700 transition-colors">Socios</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Nuevo Socio</span>
</div>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Registrar nuevo socio</h1>
        <p class="text-sm text-slate-500 mt-0.5">El número de socio se asignará automáticamente.</p>
    </div>
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

<form method="POST" action="{{ route('socios.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    @include('socios._form')

    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ route('socios.index') }}"
            class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            Cancelar
        </a>
        <button type="submit"
            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            Registrar socio
        </button>
    </div>
</form>

@endsection
