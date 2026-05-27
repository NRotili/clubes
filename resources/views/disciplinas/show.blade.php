@extends('layouts.app')

@section('title', $disciplina->nombre)

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('disciplinas.index') }}" class="hover:text-slate-700 transition-colors">Disciplinas</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">{{ $disciplina->nombre }}</span>
</div>

{{-- Encabezado --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ $disciplina->nombre }}</h1>
                @php
                    $estadoClase = $disciplina->estado === 'activa'
                        ? 'bg-green-50 text-green-700 border-green-200'
                        : 'bg-slate-100 text-slate-500 border-slate-200';
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $estadoClase }}">
                    {{ ucfirst($disciplina->estado) }}
                </span>
            </div>
            @if($disciplina->descripcion)
                <p class="text-sm text-slate-500">{{ $disciplina->descripcion }}</p>
            @endif
        </div>
        <a href="{{ route('disciplinas.edit', $disciplina) }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
            </svg>
            Editar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Info --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Información</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Costo</dt>
                <dd class="font-semibold text-slate-900">${{ number_format($disciplina->costo, 2, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Tipo</dt>
                <dd class="font-medium text-slate-900">{{ \App\Models\Disciplina::etiquetaTipoCosto($disciplina->tipo_costo) }}</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Inscriptos activos</dt>
                <dd class="font-semibold text-slate-900">{{ $disciplina->socios->where('pivot.estado', 'activa')->count() }}</dd>
            </div>
        </dl>
    </div>

    {{-- Horarios --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden lg:col-span-2">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Horarios</h2>
        </div>
        @if($disciplina->horarios->isEmpty())
            <div class="px-5 py-6 text-center text-sm text-slate-400">Sin horarios cargados.</div>
        @else
            <div class="p-5 flex flex-wrap gap-2">
                @foreach($disciplina->horarios as $h)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm bg-blue-50 text-blue-800 border border-blue-100 font-medium">
                        {{ $h->etiqueta() }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- Profesores --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Profesores
            @if($disciplina->profesores->count())
                <span class="ml-2 bg-slate-200 text-slate-600 text-xs font-medium px-1.5 py-0.5 rounded-full">{{ $disciplina->profesores->count() }}</span>
            @endif
        </h2>
        <a href="{{ route('profesores.create') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
            + Nuevo profesor
        </a>
    </div>

    {{-- Asignar --}}
    @if($profesoresDisponibles->isNotEmpty())
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
        <form method="POST" action="{{ route('disciplinas.profesores.asignar', $disciplina) }}"
            class="flex flex-col sm:flex-row items-end gap-3">
            @csrf
            <div class="flex-1 min-w-0">
                <label for="profesor_id" class="block text-xs font-medium text-slate-600 mb-1">Asignar profesor</label>
                <select id="profesor_id" name="profesor_id"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar…</option>
                    @foreach($profesoresDisponibles as $p)
                        <option value="{{ $p->id }}">{{ $p->nombreCompleto() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="shrink-0">
                <label for="sueldo" class="block text-xs font-medium text-slate-600 mb-1">Sueldo mensual</label>
                <div class="flex rounded-lg border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 w-36">
                    <span class="px-3 py-2 bg-slate-100 text-slate-500 text-sm border-r border-slate-300 select-none">$</span>
                    <input type="number" id="sueldo" name="sueldo" min="0" step="0.01" placeholder="0,00"
                        class="flex-1 px-3 py-2 text-sm bg-white focus:outline-none text-right w-0">
                </div>
            </div>
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm whitespace-nowrap">
                Asignar
            </button>
        </form>
    </div>
    @endif

    {{-- Lista de profesores asignados --}}
    @if($disciplina->profesores->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-slate-400">
            No hay profesores asignados a esta disciplina.
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($disciplina->profesores as $profesor)
                <li class="flex items-center gap-4 px-5 py-3.5">
                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0">
                        <span class="text-sm font-semibold text-slate-500">
                            {{ mb_strtoupper(mb_substr($profesor->nombre, 0, 1) . mb_substr($profesor->apellido, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('profesores.show', $profesor) }}" class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors">
                            {{ $profesor->nombreCompleto() }}
                        </a>
                        @if($profesor->email || $profesor->celular)
                            <p class="text-xs text-slate-400 mt-0.5">{{ $profesor->celular ?: $profesor->email }}</p>
                        @endif
                    </div>
                    <div class="text-sm font-semibold text-slate-700 shrink-0">
                        ${{ number_format($profesor->pivot->sueldo, 2, ',', '.') }}<span class="text-xs font-normal text-slate-400">/mes</span>
                    </div>
                    <form method="POST" action="{{ route('disciplinas.profesores.quitar', [$disciplina, $profesor]) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="text-xs text-red-400 hover:text-red-600 font-medium transition-colors"
                            onclick="return confirm('¿Quitar a {{ addslashes($profesor->nombreCompleto()) }} de esta disciplina?')">
                            Quitar
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-between text-sm font-semibold">
            <span class="text-slate-500">Costo total docente</span>
            <span class="text-slate-900">${{ number_format($disciplina->profesores->sum('pivot.sueldo'), 2, ',', '.') }}/mes</span>
        </div>
    @endif
</div>

{{-- Inscribir nuevo socio --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Inscribir Socio</h2>
    </div>
    <div class="p-5">
        @if($sociosDisponibles->isEmpty())
            <p class="text-sm text-slate-400 italic">Todos los socios ya están inscriptos en esta disciplina.</p>
        @else
            <form method="POST" action="{{ route('disciplinas.inscribir', $disciplina) }}"
                class="flex flex-col sm:flex-row items-end gap-3">
                @csrf
                <div class="flex-1 min-w-0">
                    <label for="socio_id" class="block text-sm font-medium text-slate-700 mb-1.5">Socio</label>
                    <select id="socio_id" name="socio_id"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar socio…</option>
                        @foreach($sociosDisponibles as $s)
                            <option value="{{ $s->id }}">N° {{ $s->numero_socio }} — {{ $s->nombreCompleto() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="fecha_inscripcion" class="block text-sm font-medium text-slate-700 mb-1.5">Fecha de inscripción</label>
                    <input type="date" id="fecha_inscripcion" name="fecha_inscripcion"
                        value="{{ now()->format('Y-m-d') }}"
                        class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm whitespace-nowrap">
                    Inscribir
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Lista de inscriptos --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Socios Inscriptos
            @if($disciplina->socios->count() > 0)
                <span class="ml-2 bg-slate-200 text-slate-600 text-xs font-medium px-1.5 py-0.5 rounded-full">
                    {{ $disciplina->socios->count() }}
                </span>
            @endif
        </h2>
    </div>

    @if($disciplina->socios->isEmpty())
        <div class="px-5 py-10 text-center text-sm text-slate-400">
            Todavía no hay socios inscriptos en esta disciplina.
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($disciplina->socios->sortBy('apellido') as $socio)
                @php $activa = $socio->pivot->estado === 'activa'; $beca = (bool) $socio->pivot->beca; @endphp
                <li class="flex items-center gap-4 px-5 py-3.5 {{ !$activa ? 'opacity-60' : '' }}">
                    <div class="w-9 h-9 rounded-full overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center">
                        @if($socio->fotoUrl())
                            <img src="{{ $socio->fotoUrl() }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="text-sm font-semibold text-slate-500">
                                {{ mb_strtoupper(mb_substr($socio->nombre, 0, 1) . mb_substr($socio->apellido, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('socios.show', $socio) }}" class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                {{ $socio->nombreCompleto() }}
                            </a>
                            @if($beca)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">Beca</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            N° {{ $socio->numero_socio }}
                            &middot; Inscripto el {{ \Carbon\Carbon::parse($socio->pivot->fecha_inscripcion)->format('d/m/Y') }}
                            @if(!$activa)
                                &middot; <span class="text-red-500 font-medium">Baja</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        {{-- Toggle beca --}}
                        @if($activa)
                            <form method="POST" action="{{ route('disciplinas.beca', [$disciplina, $socio]) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-medium transition-colors {{ $beca ? 'text-purple-600 hover:text-purple-800' : 'text-slate-400 hover:text-purple-600' }}">
                                    {{ $beca ? 'Quitar beca' : 'Beca' }}
                                </button>
                            </form>
                        @endif
                        {{-- Alta / Baja --}}
                        @if($activa)
                            <form method="POST" action="{{ route('disciplinas.baja', [$disciplina, $socio]) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors"
                                    onclick="return confirm('¿Dar de baja a {{ addslashes($socio->nombreCompleto()) }} de esta disciplina?')">
                                    Dar de baja
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('disciplinas.reactivar', [$disciplina, $socio]) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-green-600 hover:text-green-700 font-medium transition-colors">
                                    Reactivar
                                </button>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

@endsection
