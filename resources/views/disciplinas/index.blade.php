@extends('layouts.app')

@section('title', 'Disciplinas')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Disciplinas</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $disciplinas->count() }} {{ $disciplinas->count() === 1 ? 'disciplina registrada' : 'disciplinas registradas' }}
        </p>
    </div>
    <a href="{{ route('disciplinas.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nueva Disciplina
    </a>
</div>

@if($disciplinas->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
        </svg>
        <p class="text-slate-500 font-medium">No hay disciplinas registradas</p>
        <a href="{{ route('disciplinas.create') }}" class="inline-block mt-4 text-sm text-blue-600 font-medium hover:underline">
            Creá la primera disciplina →
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($disciplinas as $disciplina)
            @php
                $estadoClase = $disciplina->estado === 'activa'
                    ? 'bg-green-50 text-green-700 border-green-200'
                    : 'bg-slate-100 text-slate-500 border-slate-200';
            @endphp
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 flex-1">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h2 class="font-semibold text-slate-900 text-base leading-tight">{{ $disciplina->nombre }}</h2>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $estadoClase }}">
                            {{ ucfirst($disciplina->estado) }}
                        </span>
                    </div>

                    @if($disciplina->descripcion)
                        <p class="text-xs text-slate-500 mb-3 leading-relaxed line-clamp-2">{{ $disciplina->descripcion }}</p>
                    @endif

                    <div class="flex items-center gap-4 text-sm mb-3">
                        <div>
                            <span class="text-slate-400 text-xs">Costo</span>
                            <p class="font-semibold text-slate-900">${{ number_format($disciplina->costo, 2, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">{{ \App\Models\Disciplina::etiquetaTipoCosto($disciplina->tipo_costo) }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-xs">Inscriptos activos</span>
                            <p class="font-semibold text-slate-900">{{ $disciplina->socios_activos_count }}</p>
                        </div>
                    </div>

                    @if($disciplina->horarios->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($disciplina->horarios as $h)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $h->etiqueta() }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Sin horarios cargados</p>
                    @endif
                </div>

                <div class="border-t border-slate-100 px-5 py-3 bg-slate-50 flex items-center justify-between">
                    <a href="{{ route('disciplinas.show', $disciplina) }}"
                        class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                        Ver detalle →
                    </a>
                    <a href="{{ route('disciplinas.edit', $disciplina) }}"
                        class="text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                        Editar
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
