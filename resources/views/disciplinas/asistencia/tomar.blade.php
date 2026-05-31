@extends('layouts.app')

@section('title', 'Tomar lista — ' . $disciplina->nombre)

@section('content')

{{-- Breadcrumb --}}
<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('disciplinas.index') }}" class="hover:text-slate-700 transition-colors">Disciplinas</a>
    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <a href="{{ route('disciplinas.show', $disciplina) }}" class="hover:text-slate-700 transition-colors">{{ $disciplina->nombre }}</a>
    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <a href="{{ route('disciplinas.asistencia.planilla', $disciplina) }}" class="hover:text-slate-700 transition-colors">Asistencia</a>
    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Tomar lista</span>
</div>

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Tomar lista — {{ $disciplina->nombre }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">Marcá los presentes para la clase del día seleccionado.</p>
    </div>
</div>

{{-- Selector de fecha --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 mb-5">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha de la clase</label>
            <input type="date" id="fecha-picker" value="{{ $fecha }}"
                class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto"
                onchange="window.location.href='?fecha='+this.value">
        </div>

        @if(!$esDiaDeClase)
            <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
                Este día no es un horario habitual de la disciplina.
            </div>
        @endif

        @if($yaRegistrado)
            <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                </svg>
                Ya hay un registro para esta fecha. Podés modificarlo.
            </div>
        @endif
    </div>
</div>

@if($socios->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-10 text-center text-slate-400">
        No hay socios activos inscriptos en esta disciplina.
    </div>
@else
<form method="POST" action="{{ route('disciplinas.asistencia.store', $disciplina) }}" id="form-asistencia">
    @csrf
    <input type="hidden" name="fecha" value="{{ $fecha }}">

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-5">
        {{-- Toolbar --}}
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Socios inscriptos
                    <span class="ml-1.5 bg-slate-200 text-slate-600 text-xs font-medium px-1.5 py-0.5 rounded-full">{{ $socios->count() }}</span>
                </h2>
                <span id="contador" class="text-xs text-blue-600 font-medium ml-2"></span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleTodos(true)"
                    class="text-xs font-medium text-slate-600 hover:text-slate-900 px-2 py-1 rounded hover:bg-slate-100 transition-colors">
                    Todos
                </button>
                <button type="button" onclick="toggleTodos(false)"
                    class="text-xs font-medium text-slate-600 hover:text-slate-900 px-2 py-1 rounded hover:bg-slate-100 transition-colors">
                    Ninguno
                </button>
            </div>
        </div>

        {{-- Lista --}}
        <ul class="divide-y divide-slate-100" id="lista-socios">
            @foreach($socios as $socio)
                @php $presente = $presentes->contains($socio->id); @endphp
                <li class="flex items-center gap-4 px-5 py-3.5 cursor-pointer hover:bg-slate-50 transition-colors"
                    onclick="toggleSocio({{ $socio->id }})">
                    <div class="relative shrink-0">
                        <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                            @if($socio->fotoUrl())
                                <img src="{{ $socio->fotoUrl() }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-semibold text-slate-500">
                                    {{ mb_strtoupper(mb_substr($socio->nombre, 0, 1) . mb_substr($socio->apellido, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div id="check-{{ $socio->id }}"
                            class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center transition-colors
                            {{ $presente ? 'bg-green-500' : 'bg-slate-200' }}">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900">{{ $socio->nombreCompleto() }}</p>
                        <p class="text-xs text-slate-400">N° {{ $socio->numero_socio }}</p>
                    </div>
                    <input type="checkbox" name="presentes[]" value="{{ $socio->id }}"
                        id="cb-{{ $socio->id }}"
                        class="hidden"
                        {{ $presente ? 'checked' : '' }}>
                    <div id="estado-{{ $socio->id }}"
                        class="text-xs font-semibold px-2.5 py-1 rounded-full transition-colors
                        {{ $presente ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-400' }}">
                        {{ $presente ? 'Presente' : 'Ausente' }}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('disciplinas.asistencia.planilla', $disciplina) }}"
            class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            Cancelar
        </a>
        <button type="submit"
            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            Guardar asistencia
        </button>
    </div>
</form>
@endif

<script>
function toggleSocio(id) {
    const cb     = document.getElementById('cb-' + id);
    const check  = document.getElementById('check-' + id);
    const estado = document.getElementById('estado-' + id);
    cb.checked = !cb.checked;

    if (cb.checked) {
        check.classList.replace('bg-slate-200', 'bg-green-500');
        estado.className = 'text-xs font-semibold px-2.5 py-1 rounded-full transition-colors bg-green-50 text-green-700 border border-green-200';
        estado.textContent = 'Presente';
    } else {
        check.classList.replace('bg-green-500', 'bg-slate-200');
        estado.className = 'text-xs font-semibold px-2.5 py-1 rounded-full transition-colors bg-slate-100 text-slate-400';
        estado.textContent = 'Ausente';
    }
    actualizarContador();
}

function toggleTodos(marcar) {
    document.querySelectorAll('[id^="cb-"]').forEach(cb => {
        const id = cb.value;
        if (cb.checked !== marcar) {
            cb.checked = marcar;
            const check  = document.getElementById('check-' + id);
            const estado = document.getElementById('estado-' + id);
            if (marcar) {
                check.classList.replace('bg-slate-200', 'bg-green-500');
                estado.className = 'text-xs font-semibold px-2.5 py-1 rounded-full transition-colors bg-green-50 text-green-700 border border-green-200';
                estado.textContent = 'Presente';
            } else {
                check.classList.replace('bg-green-500', 'bg-slate-200');
                estado.className = 'text-xs font-semibold px-2.5 py-1 rounded-full transition-colors bg-slate-100 text-slate-400';
                estado.textContent = 'Ausente';
            }
        }
    });
    actualizarContador();
}

function actualizarContador() {
    const total    = document.querySelectorAll('[id^="cb-"]').length;
    const presentes = document.querySelectorAll('[id^="cb-"]:checked').length;
    document.getElementById('contador').textContent = presentes + ' de ' + total + ' presentes';
}

document.addEventListener('DOMContentLoaded', actualizarContador);
</script>

@endsection
