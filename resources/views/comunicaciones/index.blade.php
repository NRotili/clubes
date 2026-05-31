@extends('layouts.app')
@section('title', 'Comunicaciones')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Comunicaciones</h1>
        <p class="text-sm text-slate-500 mt-0.5">Enviá emails a socios individuales o en grupo.</p>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Formulario --}}
        <div class="lg:col-span-3">
            <form method="POST" action="{{ route('comunicaciones.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-5">
                @csrf

                <h2 class="text-sm font-semibold text-slate-700">Nueva comunicación</h2>

                {{-- Destinatarios --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Destinatarios</label>
                    <div class="space-y-2" x-data="{ tipo: '{{ old('destinatario_tipo', 'todos') }}' }">
                        @foreach([
                            'todos'     => 'Todos los socios activos',
                            'deudores'  => 'Solo deudores (cuotas impagas)',
                            'categoria' => 'Por categoría',
                        ] as $val => $label)
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="destinatario_tipo" value="{{ $val }}"
                                    @checked(old('destinatario_tipo', 'todos') === $val)
                                    x-on:change="tipo = '{{ $val }}'"
                                    class="text-blue-600">
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach

                        {{-- Filtro de categoría --}}
                        <div x-show="tipo === 'categoria'" class="ml-6 mt-1">
                            <select name="filtro" class="border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat }}" @selected(old('filtro') === $cat)>
                                        {{ \App\Models\Socio::etiquetaCategoria($cat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Asunto --}}
                <div>
                    <label for="asunto" class="block text-sm font-medium text-slate-700 mb-1.5">Asunto</label>
                    <input type="text" id="asunto" name="asunto" value="{{ old('asunto') }}"
                        placeholder="Ej: Recordatorio de cuota — Junio 2026"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('asunto') border-red-400 @enderror">
                    @error('asunto')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Cuerpo --}}
                <div>
                    <label for="cuerpo" class="block text-sm font-medium text-slate-700 mb-1.5">Mensaje</label>
                    <textarea id="cuerpo" name="cuerpo" rows="8"
                        placeholder="Escribí el mensaje aquí. Podés usar saltos de línea."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y @error('cuerpo') border-red-400 @enderror">{{ old('cuerpo') }}</textarea>
                    @error('cuerpo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Templates rápidos --}}
                <div>
                    <p class="text-xs font-medium text-slate-500 mb-2">Plantillas rápidas:</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="usarPlantilla('recordatorio')"
                            class="text-xs px-3 py-1.5 border border-slate-300 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
                            Recordatorio de cuota
                        </button>
                        <button type="button" onclick="usarPlantilla('suspension')"
                            class="text-xs px-3 py-1.5 border border-slate-300 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
                            Aviso de suspensión
                        </button>
                        <button type="button" onclick="usarPlantilla('bienvenida')"
                            class="text-xs px-3 py-1.5 border border-slate-300 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
                            Bienvenida
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <p class="text-xs text-slate-400">Los emails se envían de forma asíncrona.</p>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        Enviar comunicación
                    </button>
                </div>
            </form>
        </div>

        {{-- Historial --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-700">Historial reciente</h2>
                </div>
                @forelse($historial as $com)
                    <div class="px-5 py-4 border-b border-slate-50 last:border-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $com->asunto }}</p>
                            <span class="text-xs font-semibold text-emerald-600 shrink-0">{{ $com->enviados }} env.</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ \App\Models\Comunicacion::etiquetaDestinatario($com->destinatario_tipo, $com->filtro) }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            {{ $com->usuario->name ?? '—' }} · {{ $com->created_at->diffForHumans() }}
                        </p>
                    </div>
                @empty
                    <p class="px-5 py-8 text-sm text-slate-400 text-center">Sin comunicaciones enviadas aún.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

<script>
const plantillas = {
    recordatorio: {
        asunto: 'Recordatorio de cuota pendiente',
        cuerpo: `Te informamos que tenés una o más cuotas pendientes de pago en el club.\n\nTe pedimos que regularices tu situación a la brevedad para evitar inconvenientes en el acceso a las instalaciones y actividades.\n\nPodés acercarte a la administración en el horario habitual o consultar el estado de tu cuenta desde la app del club.\n\n¡Muchas gracias!`
    },
    suspension: {
        asunto: 'Aviso importante: suspensión de cuenta',
        cuerpo: `Te informamos que tu cuenta en el club ha sido suspendida por presentar cuotas impagas.\n\nPara reactivar tu membresía, necesitás regularizar los pagos pendientes en la administración del club.\n\nQuedamos a tu disposición para cualquier consulta.\n\nAtentamente,\nAdministración del club`
    },
    bienvenida: {
        asunto: '¡Bienvenido/a al club!',
        cuerpo: `Es un placer darte la bienvenida a nuestra familia.\n\nYa podés acceder a todas las instalaciones y actividades del club con tu carnet de socio.\n\nPara cualquier consulta, no dudes en acercarte a la administración. También podés gestionar tus datos y cuotas desde la app del club.\n\n¡Te esperamos!`
    }
};

function usarPlantilla(tipo) {
    const p = plantillas[tipo];
    document.getElementById('asunto').value = p.asunto;
    document.getElementById('cuerpo').value = p.cuerpo;
}
</script>

@endsection
