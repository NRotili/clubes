@extends('layouts.app')

@section('title', 'Agenda — ' . $actividad->nombre)

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
    <span class="text-slate-900 font-medium">Agenda</span>
</div>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Agenda — {{ $actividad->nombre }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ ucfirst($fecha->translatedFormat('l d \\d\\e F \\d\\e Y')) }}</p>
    </div>

    <form method="GET" action="{{ route('actividades.agenda', $actividad) }}" class="flex items-center gap-2">
        <a href="{{ route('actividades.agenda', ['actividad' => $actividad, 'fecha' => $fecha->copy()->subDay()->format('Y-m-d')]) }}"
            class="p-2 text-slate-500 hover:text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
        </a>
        <input type="date" name="fecha" value="{{ $fecha->format('Y-m-d') }}" onchange="this.form.submit()"
            class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <a href="{{ route('actividades.agenda', ['actividad' => $actividad, 'fecha' => $fecha->copy()->addDay()->format('Y-m-d')]) }}"
            class="p-2 text-slate-500 hover:text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
            </svg>
        </a>
        <a href="{{ route('actividades.agenda', $actividad) }}"
            class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">
            Hoy
        </a>
    </form>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
        </svg>
        {{ session('error') }}
    </div>
@endif
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

@if(empty($slots))
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
        </svg>
        <p class="text-slate-500 font-medium">Esta actividad no tiene franjas configuradas para este día</p>
        <a href="{{ route('actividades.edit', $actividad) }}" class="inline-block mt-4 text-sm text-blue-600 font-medium hover:underline">
            Configurar franjas →
        </a>
    </div>
@else

    {{-- Reserva manual --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cargar turno manual</h2>
        </div>
        <div class="p-5">
            @php $slotsConCupo = collect($slots)->where('disponibles', '>', 0); @endphp
            @if($slotsConCupo->isEmpty())
                <p class="text-sm text-slate-400 italic">No quedan turnos con cupo disponible para este día.</p>
            @else
                <form method="POST" action="{{ route('actividades.turnos.store', $actividad) }}"
                    class="flex flex-col sm:flex-row items-end gap-3">
                    @csrf
                    <input type="hidden" name="fecha" value="{{ $fecha->format('Y-m-d') }}">
                    <div class="flex-1 min-w-0">
                        <label for="socio_id" class="block text-sm font-medium text-slate-700 mb-1.5">Socio</label>
                        <select id="socio_id" name="socio_id" required
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar socio…</option>
                            @foreach($sociosDisponibles as $s)
                                <option value="{{ $s->id }}">N° {{ $s->numero_socio }} — {{ $s->nombreCompleto() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="hora_inicio" class="block text-sm font-medium text-slate-700 mb-1.5">Turno</label>
                        <select id="hora_inicio" name="hora_inicio" required
                            class="px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($slotsConCupo as $slot)
                                <option value="{{ $slot['hora_inicio'] }}">
                                    {{ $slot['hora_inicio'] }} – {{ $slot['hora_fin'] }} ({{ $slot['disponibles'] }}/{{ $slot['cupo'] }} disponibles)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="observaciones" class="block text-sm font-medium text-slate-700 mb-1.5">Observaciones</label>
                        <input type="text" id="observaciones" name="observaciones" maxlength="500"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm whitespace-nowrap">
                        Reservar
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Slots del día --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($slots as $slot)
            @php
                $lleno = $slot['disponibles'] <= 0;
            @endphp
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="font-semibold text-slate-900 text-sm">{{ $slot['hora_inicio'] }} – {{ $slot['hora_fin'] }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                        {{ $lleno ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }}">
                        {{ $slot['ocupados'] }} / {{ $slot['cupo'] }}
                    </span>
                </div>

                @if($slot['turnos']->isEmpty())
                    <div class="px-5 py-6 text-center text-sm text-slate-400">Sin reservas para este turno.</div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($slot['turnos'] as $turno)
                            @php
                                $estadoClase = match($turno->estado) {
                                    'pendiente'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'confirmado' => 'bg-green-50 text-green-700 border-green-200',
                                    default      => 'bg-slate-100 text-slate-500 border-slate-200',
                                };
                            @endphp
                            <li class="px-5 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <a href="{{ route('socios.show', $turno->socio) }}" class="text-sm font-medium text-slate-900 hover:text-blue-600 transition-colors">
                                            {{ $turno->socio->nombreCompleto() }}
                                        </a>
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $estadoClase }}">
                                                {{ \App\Models\ActividadTurno::etiquetaEstado($turno->estado) }}
                                            </span>
                                            @if($turno->monto !== null)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs border
                                                    {{ $turno->pagado ? 'bg-green-50 text-green-700 border-green-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                                    ${{ number_format($turno->monto, 2, ',', '.') }} {{ $turno->pagado ? 'pagado' : 'pendiente de pago' }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($turno->observaciones)
                                            <p class="text-xs text-slate-400 mt-1">{{ $turno->observaciones }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                                        @if($turno->estado === 'pendiente')
                                            <div class="flex items-center gap-2">
                                                <form method="POST" action="{{ route('turnos.aprobar', $turno) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-xs font-medium text-green-600 hover:text-green-800 transition-colors">Aprobar</button>
                                                </form>
                                                <form method="POST" action="{{ route('turnos.rechazar', $turno) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition-colors">Rechazar</button>
                                                </form>
                                            </div>
                                        @endif
                                        @if(in_array($turno->estado, ['pendiente', 'confirmado']))
                                            <form method="POST" action="{{ route('turnos.cancelar', $turno) }}"
                                                onsubmit="return confirm('¿Cancelar este turno?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs font-medium text-slate-400 hover:text-red-600 transition-colors">Cancelar</button>
                                            </form>
                                        @endif
                                        @if($turno->monto !== null && !$turno->pagado)
                                            <form method="POST" action="{{ route('turnos.pagado', $turno) }}" class="flex items-center gap-1.5">
                                                @csrf @method('PATCH')
                                                <select name="metodo_pago" class="text-xs border border-slate-300 rounded-md px-1.5 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="transferencia">Transferencia</option>
                                                    <option value="tarjeta_debito">Tarjeta débito</option>
                                                </select>
                                                <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors whitespace-nowrap">Marcar pagado</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
@endif

@endsection
