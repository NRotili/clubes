@extends('layouts.app')

@section('title', 'Mis clases')

@section('content')

<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Mis clases</h1>
        <p class="text-sm text-slate-500 mt-1">Tomá lista o consultá la planilla de asistencia de cada disciplina.</p>
    </div>
    @if(auth()->user()->socio_id)
        <a href="{{ route('socios.show', auth()->user()->socio_id) }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
            Mi perfil de socio
        </a>
    @endif
</div>

@if($disciplinas->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-16 text-center text-slate-400">
        No tenés disciplinas asignadas actualmente.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($disciplinas as $disciplina)
            @php $clasesEsteMes = $clasesMes[$disciplina->id]->total ?? 0; @endphp
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $disciplina->nombre }}</h2>
                            @if($disciplina->horarios->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach($disciplina->horarios as $h)
                                        <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-full font-medium">
                                            {{ $h->etiqueta() }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if($clasesEsteMes > 0)
                            <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                                {{ $clasesEsteMes }} {{ $clasesEsteMes === 1 ? 'clase' : 'clases' }} este mes
                            </span>
                        @endif
                    </div>
                </div>

                <div class="px-5 py-4 flex items-center gap-3">
                    <a href="{{ route('disciplinas.asistencia.tomar', $disciplina) }}"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Tomar lista hoy
                    </a>
                    <a href="{{ route('disciplinas.asistencia.planilla', $disciplina) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0 1 18 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0 1 18 7.875v1.5m1.125-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125M18 5.625v5.25M7.125 12h9.75m-9.75 0A1.125 1.125 0 0 1 6 10.875M7.125 12C6.504 12 6 12.504 6 13.125m0-2.25C6 11.496 5.496 12 4.875 12M18 10.875c0 .621-.504 1.125-1.125 1.125M18 10.875c0 .621.504 1.125 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-9.75 0h9.75"/>
                        </svg>
                        Ver planilla
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
