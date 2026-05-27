@extends('layouts.app')

@section('title', 'Nuevo Profesor')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('profesores.index') }}" class="hover:text-slate-700 transition-colors">Profesores</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Nuevo profesor</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Nuevo profesor</h1>
</div>

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

<form method="POST" action="{{ route('profesores.store') }}" novalidate>
    @csrf
    @include('profesores._form')
    <div class="flex items-center justify-end gap-3 mt-6">
        <a href="{{ route('profesores.index') }}"
            class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            Cancelar
        </a>
        <button type="submit"
            class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            Guardar profesor
        </button>
    </div>
</form>

@endsection
