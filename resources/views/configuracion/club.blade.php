@extends('layouts.app')

@section('title', 'Personalización del Club')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <span class="text-slate-400">Configuración</span>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Club</span>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Personalización del Club</h1>
    <p class="text-sm text-slate-500 mt-0.5">Nombre, logo y datos de contacto que aparecen en toda la aplicación.</p>
</div>

<form method="POST" action="{{ route('club.config.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Logo --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-700">Logo del club</h2>
                </div>
                <div class="p-5 flex flex-col items-center gap-4">

                    {{-- Preview actual --}}
                    <div class="w-32 h-32 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden" id="logo-preview-wrap">
                        @if($logo_url)
                            <img src="{{ $logo_url }}" alt="Logo" class="w-full h-full object-contain p-2" id="logo-preview">
                        @else
                            <div id="logo-placeholder" class="flex flex-col items-center gap-1 text-slate-400">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                </svg>
                                <span class="text-xs">Sin logo</span>
                            </div>
                            <img src="" alt="" class="w-full h-full object-contain p-2 hidden" id="logo-preview">
                        @endif
                    </div>

                    <label for="logo"
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                        </svg>
                        Subir imagen
                    </label>
                    <input type="file" id="logo" name="logo" accept="image/*" class="hidden">
                    <p class="text-xs text-slate-400 text-center">PNG, JPG o SVG. Máx. 2 MB.<br>Recomendado: fondo transparente.</p>

                    @if($logo_url)
                        <label class="flex items-center gap-2 text-sm text-red-500 cursor-pointer">
                            <input type="checkbox" name="eliminar_logo" value="1" class="rounded border-slate-300 text-red-500 focus:ring-red-500">
                            Eliminar logo actual
                        </label>
                    @endif
                </div>
            </div>
        </div>

        {{-- Datos --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-sm font-semibold text-slate-700">Datos del club</h2>
                </div>
                <div class="p-5 space-y-4">

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nombre del club <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre"
                            value="{{ old('nombre', $nombre) }}"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Club Atlético Ejemplo">
                        @error('nombre')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="direccion" class="block text-sm font-medium text-slate-700 mb-1.5">Dirección</label>
                        <input type="text" id="direccion" name="direccion"
                            value="{{ old('direccion', $direccion) }}"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Av. Siempre Viva 742, Ciudad">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono</label>
                            <input type="text" id="telefono" name="telefono"
                                value="{{ old('telefono', $telefono) }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="(011) 4123-4567">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email de contacto</label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $email) }}"
                                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="info@club.com">
                        </div>
                    </div>

                    <div>
                        <label for="web" class="block text-sm font-medium text-slate-700 mb-1.5">Sitio web</label>
                        <input type="url" id="web" name="web"
                            value="{{ old('web', $web) }}"
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="https://www.club.com">
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="flex justify-end mt-6">
        <button type="submit"
            class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            Guardar cambios
        </button>
    </div>
</form>

<script>
document.getElementById('logo').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('logo-preview');
        const placeholder = document.getElementById('logo-placeholder');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(file);
});
</script>

@endsection
