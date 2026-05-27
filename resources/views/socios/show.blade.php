@extends('layouts.app')

@section('title', $socio->nombreCompleto())

@section('content')

{{-- Migas de pan --}}
<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('socios.index') }}" class="hover:text-slate-700 transition-colors">Socios</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">{{ $socio->nombreCompleto() }}</span>
</div>

{{-- Tarjeta de encabezado --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center shrink-0 border border-slate-200">
                @if($socio->fotoUrl())
                    <img src="{{ $socio->fotoUrl() }}" alt="Foto de {{ $socio->nombreCompleto() }}" class="w-full h-full object-cover">
                @else
                    <span class="text-xl font-bold text-blue-700">
                        {{ mb_strtoupper(mb_substr($socio->nombre, 0, 1) . mb_substr($socio->apellido, 0, 1)) }}
                    </span>
                @endif
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ $socio->nombreCompleto() }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    <span class="font-mono text-sm text-slate-500">N° {{ $socio->numero_socio }}</span>
                    <span class="text-slate-300">·</span>

                    @php
                        $estadoClases = [
                            'activo'     => 'bg-green-50 text-green-700 border-green-200',
                            'pendiente'  => 'bg-amber-50 text-amber-700 border-amber-200',
                            'suspendido' => 'bg-orange-50 text-orange-700 border-orange-200',
                            'inactivo'   => 'bg-slate-100 text-slate-600 border-slate-200',
                        ];
                        $dotClases = [
                            'activo'     => 'bg-green-500',
                            'pendiente'  => 'bg-amber-500',
                            'suspendido' => 'bg-orange-500',
                            'inactivo'   => 'bg-slate-400',
                        ];
                        $catClases = [
                            'adulto'   => 'bg-blue-50 text-blue-700 border-blue-200',
                            'junior'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'cadete'   => 'bg-violet-50 text-violet-700 border-violet-200',
                            'bebe'     => 'bg-pink-50 text-pink-700 border-pink-200',
                            'jubilado' => 'bg-slate-100 text-slate-600 border-slate-200',
                        ];
                    @endphp

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $estadoClases[$socio->estado] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dotClases[$socio->estado] ?? 'bg-slate-400' }}"></span>
                        {{ \App\Models\Socio::etiquetaEstado($socio->estado) }}
                    </span>

                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $catClases[$socio->categoria] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                        {{ \App\Models\Socio::etiquetaCategoria($socio->categoria) }}
                    </span>

                    @if(!$socio->esTitular())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72"/>
                            </svg>
                            Familiar
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                            Titular
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if(auth()->user()->puedeGestionarSocios())
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($socio->esTitular() && $socio->grupoFamiliar->isNotEmpty())
                    <a href="{{ route('pagos.create-familiar', ['socio_id' => $socio->id]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 border border-green-600 rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/>
                        </svg>
                        Pagar grupo familiar
                    </a>
                @endif
                <a href="{{ route('socios.edit', $socio) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                    </svg>
                    Editar
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Grilla de datos --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Datos personales --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Datos Personales</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Fecha de nacimiento</dt>
                <dd class="font-medium text-slate-900">{{ $socio->fecha_nacimiento->format('d/m/Y') }}</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Edad</dt>
                <dd class="font-medium text-slate-900">{{ $socio->edad() }} años</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Género</dt>
                <dd class="font-medium text-slate-900">{{ \App\Models\Socio::etiquetaGenero($socio->genero) }}</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">{{ $socio->tipo_documento }}</dt>
                <dd class="font-medium text-slate-900">{{ $socio->numero_documento }}</dd>
            </div>
        </dl>
    </div>

    {{-- Contacto --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Contacto</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between px-5 py-3 text-sm gap-4">
                <dt class="text-slate-500 shrink-0">Email</dt>
                <dd class="font-medium text-slate-900 text-right truncate">
                    {{ $socio->email ?? '—' }}
                </dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Celular</dt>
                <dd class="font-medium text-slate-900">{{ $socio->celular ?? '—' }}</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Teléfono</dt>
                <dd class="font-medium text-slate-900">{{ $socio->telefono ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Membresía --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Membresía</h2>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Fecha de alta</dt>
                <dd class="font-medium text-slate-900">{{ $socio->fecha_alta->format('d/m/Y') }}</dd>
            </div>
            <div class="flex justify-between px-5 py-3 text-sm">
                <dt class="text-slate-500">Antigüedad</dt>
                <dd class="font-medium text-slate-900">{{ $socio->fecha_alta->diffForHumans(null, true) }}</dd>
            </div>
            @if($socio->observaciones)
                <div class="px-5 py-3 text-sm">
                    <dt class="text-slate-500 mb-1">Observaciones</dt>
                    <dd class="text-slate-700 text-xs leading-relaxed">{{ $socio->observaciones }}</dd>
                </div>
            @endif
        </dl>
    </div>

</div>

{{-- Domicilio --}}
@if($socio->direccion || $socio->ciudad || $socio->provincia)
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Domicilio</h2>
        </div>
        <div class="px-5 py-4 text-sm text-slate-700">
            {{ collect([$socio->direccion, $socio->ciudad, $socio->provincia, $socio->codigo_postal])->filter()->implode(', ') }}
        </div>
    </div>
@endif

{{-- Grupo familiar --}}
@if(!$socio->esTitular())
    {{-- Es familiar de un titular --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Grupo Familiar</h2>
        </div>
        <div class="p-5">
            <p class="text-sm text-slate-600 mb-3">
                Este socio integra el grupo familiar de:
            </p>
            <div class="flex items-center gap-4 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <span class="text-sm font-bold text-blue-700">
                        {{ mb_strtoupper(mb_substr($socio->titular->nombre, 0, 1) . mb_substr($socio->titular->apellido, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-1">
                    <a href="{{ route('socios.show', $socio->titular) }}" class="font-semibold text-slate-900 hover:text-blue-600 transition-colors">
                        {{ $socio->titular->nombreCompleto() }}
                    </a>
                    <p class="text-xs text-slate-500 mt-0.5">
                        N° {{ $socio->titular->numero_socio }} &mdash;
                        Vínculo: <span class="font-medium">{{ \App\Models\Socio::etiquetaParentesco($socio->parentesco) }}</span>
                    </p>
                </div>
                <a href="{{ route('socios.show', $socio->titular) }}"
                    class="text-slate-400 hover:text-blue-600 transition-colors p-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
@else
    {{-- Es titular — mostrar su grupo familiar --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Grupo Familiar
                @if($socio->grupoFamiliar->count() > 0)
                    <span class="ml-2 bg-slate-200 text-slate-600 text-xs font-medium px-1.5 py-0.5 rounded-full">
                        {{ $socio->grupoFamiliar->count() }}
                    </span>
                @endif
            </h2>
            @if(auth()->user()->puedeGestionarSocios())
            <a href="{{ route('socios.create', ['titular_id' => $socio->id]) }}"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Agregar familiar
            </a>
            @endif
        </div>

        @if($socio->grupoFamiliar->isEmpty())
            <div class="px-5 py-8 text-center">
                <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                </svg>
                <p class="text-sm text-slate-400">Ningún familiar registrado todavía.</p>
                <a href="{{ route('socios.create', ['titular_id' => $socio->id]) }}"
                    class="mt-2 inline-block text-xs text-blue-600 hover:underline font-medium">
                    Agregar el primer familiar →
                </a>
            </div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach($socio->grupoFamiliar as $familiar)
                    <li class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                            <span class="text-sm font-semibold text-slate-600">
                                {{ mb_strtoupper(mb_substr($familiar->nombre, 0, 1) . mb_substr($familiar->apellido, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('socios.show', $familiar) }}" class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                {{ $familiar->nombreCompleto() }}
                            </a>
                            <p class="text-xs text-slate-500 mt-0.5">
                                N° {{ $familiar->numero_socio }}
                                &middot; {{ \App\Models\Socio::etiquetaParentesco($familiar->parentesco) }}
                                &middot; {{ \App\Models\Socio::etiquetaCategoria($familiar->categoria) }}
                                &middot; {{ $familiar->edad() }} años
                            </p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            @php
                                $dotClasesF = [
                                    'activo'     => 'bg-green-500',
                                    'pendiente'  => 'bg-amber-500',
                                    'suspendido' => 'bg-orange-500',
                                    'inactivo'   => 'bg-slate-400',
                                ];
                            @endphp
                            <span class="w-2 h-2 rounded-full {{ $dotClasesF[$familiar->estado] ?? 'bg-slate-400' }}" title="{{ \App\Models\Socio::etiquetaEstado($familiar->estado) }}"></span>
                            <a href="{{ route('socios.show', $familiar) }}"
                                class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif

{{-- Disciplinas --}}
@php
    $disciplinasActivas  = $socio->disciplinas->where('pivot.estado', 'activa');
    $totalDisciplinas    = $disciplinasActivas->sum(function ($d) {
        return match ($d->tipo_costo) {
            'mensual'   => $d->costo,
            'anual'     => $d->costo / 12,
            default     => 0,
        };
    });
    $tieneClases  = $disciplinasActivas->where('tipo_costo', 'por_clase')->isNotEmpty();
    $totalMensual = $cuotaBase + $totalDisciplinas;
@endphp

<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Disciplinas
            @if($disciplinasActivas->count() > 0)
                <span class="ml-2 bg-slate-200 text-slate-600 text-xs font-medium px-1.5 py-0.5 rounded-full">
                    {{ $disciplinasActivas->count() }}
                </span>
            @endif
        </h2>
        @if(auth()->user()->puedeGestionarSocios())
            <a href="{{ route('disciplinas.index') }}"
                class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
                Gestionar inscripciones →
            </a>
        @endif
    </div>

    @if($socio->disciplinas->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-slate-400">
            No está inscripto en ninguna disciplina.
        </div>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($socio->disciplinas->sortBy('nombre') as $disciplina)
                @php $activa = $disciplina->pivot->estado === 'activa'; @endphp
                <li class="flex items-center gap-4 px-5 py-3.5 {{ !$activa ? 'opacity-50' : '' }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('disciplinas.show', $disciplina) }}"
                                class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                {{ $disciplina->nombre }}
                            </a>
                            @if(!$activa)
                                <span class="text-xs text-red-500 font-medium">Baja</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Inscripto el {{ \Carbon\Carbon::parse($disciplina->pivot->fecha_inscripcion)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold text-slate-900">${{ number_format($disciplina->costo, 2, ',', '.') }}</p>
                        <p class="text-xs text-slate-400">{{ \App\Models\Disciplina::etiquetaTipoCosto($disciplina->tipo_costo) }}</p>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Resumen de costos --}}
        <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 space-y-1">

            {{-- Cuota base --}}
            <div class="flex justify-between text-xs text-slate-500">
                <span>Cuota base ({{ \App\Models\Socio::etiquetaCategoria($socio->categoria) }} / {{ \App\Models\Socio::etiquetaGenero($socio->genero) }})</span>
                <span>${{ number_format($cuotaBase, 2, ',', '.') }}</span>
            </div>

            {{-- Disciplinas --}}
            @foreach($disciplinasActivas->sortBy('nombre') as $disciplina)
                <div class="flex justify-between text-xs text-slate-500">
                    <span>{{ $disciplina->nombre }}</span>
                    <span>
                        @if($disciplina->tipo_costo === 'anual')
                            ${{ number_format($disciplina->costo / 12, 2, ',', '.') }}
                            <span class="text-slate-400">(anual ÷12)</span>
                        @elseif($disciplina->tipo_costo === 'por_clase')
                            ${{ number_format($disciplina->costo, 2, ',', '.') }}
                            <span class="text-slate-400">por clase</span>
                        @else
                            ${{ number_format($disciplina->costo, 2, ',', '.') }}
                        @endif
                    </span>
                </div>
            @endforeach

            {{-- Total --}}
            <div class="flex justify-between items-baseline pt-3 mt-2 border-t border-slate-200">
                <span class="text-sm font-semibold text-slate-700">Total mensual</span>
                <div class="text-right">
                    <span class="text-lg font-bold text-slate-900">${{ number_format($totalMensual, 2, ',', '.') }}</span>
                    @if($tieneClases)
                        <p class="text-xs text-slate-400">+ costo por clase no incluido</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

{{-- QR de acceso --}}
@if($socio->qr_uuid)
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Código QR de Acceso</h2>
        <a href="{{ route('socios.qr', $socio) }}" download="qr-socio-{{ $socio->numero_socio }}.svg"
            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Descargar QR
        </a>
    </div>
    <div class="p-6 flex flex-col sm:flex-row items-center gap-6">
        <div class="shrink-0 p-3 bg-white border border-slate-200 rounded-xl shadow-sm">
            <img src="{{ route('socios.qr', $socio) }}" alt="QR Socio {{ $socio->numero_socio }}" class="w-40 h-40">
        </div>
        <div>
            <p class="text-sm text-slate-700 font-medium mb-1">Carnet de acceso al club</p>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">
                Este código QR es único e intransferible. Al escanearlo, el sistema muestra los datos del socio y su estado para verificar el acceso.
            </p>
            <a href="{{ route('socios.verificar', $socio->qr_uuid) }}" target="_blank"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-blue-600 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                </svg>
                Ver pantalla de verificación
            </a>
        </div>
    </div>
</div>
@endif

@endsection
