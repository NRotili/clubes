@php
    $provincias = [
        'Buenos Aires', 'Ciudad Autónoma de Buenos Aires', 'Catamarca', 'Chaco',
        'Chubut', 'Córdoba', 'Corrientes', 'Entre Ríos', 'Formosa', 'Jujuy',
        'La Pampa', 'La Rioja', 'Mendoza', 'Misiones', 'Neuquén', 'Río Negro',
        'Salta', 'San Juan', 'San Luis', 'Santa Cruz', 'Santa Fe',
        'Santiago del Estero', 'Tierra del Fuego', 'Tucumán',
    ];
@endphp

{{-- ─────────────────────── Fotografía ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Fotografía</h2>
    </div>
    <div class="p-6 flex flex-col sm:flex-row items-start gap-6">

        {{-- Preview --}}
        <div class="shrink-0">
            <div id="foto-preview-wrapper" class="w-28 h-28 rounded-full overflow-hidden border-2 border-slate-200 bg-slate-100 flex items-center justify-center">
                @if(isset($socio) && $socio->fotoUrl())
                    <img id="foto-preview" src="{{ $socio->fotoUrl() }}" alt="Foto" class="w-full h-full object-cover">
                @else
                    <img id="foto-preview" src="" alt="Foto" class="w-full h-full object-cover hidden">
                    <svg id="foto-placeholder" class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                @endif
            </div>
        </div>

        <div class="flex-1">
            <label for="foto" class="block text-sm font-medium text-slate-700 mb-1.5">
                {{ isset($socio) && $socio->fotoUrl() ? 'Reemplazar fotografía' : 'Subir fotografía' }}
            </label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                    file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer
                    {{ $errors->has('foto') ? 'border border-red-400 rounded-lg' : '' }}">
            <p class="text-xs text-slate-500 mt-1.5">JPG, PNG o WebP. Máximo 3 MB.</p>
            @error('foto')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror

            @if(isset($socio) && $socio->fotoUrl())
                <label class="inline-flex items-center gap-2 mt-3 cursor-pointer">
                    <input type="checkbox" name="eliminar_foto" value="1" class="w-4 h-4 text-red-600 border-slate-300 rounded focus:ring-red-500">
                    <span class="text-xs text-red-600 font-medium">Eliminar foto actual</span>
                </label>
            @endif
        </div>

    </div>
</div>

{{-- ─────────────────────── Datos personales ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Datos Personales</h2>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- Apellido --}}
        <div>
            <label for="apellido" class="block text-sm font-medium text-slate-700 mb-1.5">
                Apellido <span class="text-red-500">*</span>
            </label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $socio->apellido ?? '') }}"
                autocomplete="family-name"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                    {{ $errors->has('apellido') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('apellido')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nombre --}}
        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1.5">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $socio->nombre ?? '') }}"
                autocomplete="given-name"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                    {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('nombre')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Género --}}
        <div>
            <label for="genero" class="block text-sm font-medium text-slate-700 mb-1.5">
                Género <span class="text-red-500">*</span>
            </label>
            <select id="genero" name="genero"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                    {{ $errors->has('genero') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <option value="">Seleccionar…</option>
                <option value="M" {{ old('genero', $socio->genero ?? '') === 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('genero', $socio->genero ?? '') === 'F' ? 'selected' : '' }}>Femenino</option>
                <option value="X" {{ old('genero', $socio->genero ?? '') === 'X' ? 'selected' : '' }}>No binario / Otro</option>
            </select>
            @error('genero')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Fecha de nacimiento --}}
        <div>
            <label for="fecha_nacimiento" class="block text-sm font-medium text-slate-700 mb-1.5">
                Fecha de nacimiento <span class="text-red-500">*</span>
            </label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                value="{{ old('fecha_nacimiento', isset($socio) ? $socio->fecha_nacimiento?->format('Y-m-d') : '') }}"
                max="{{ now()->subDay()->format('Y-m-d') }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                    {{ $errors->has('fecha_nacimiento') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('fecha_nacimiento')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tipo de documento --}}
        <div>
            <label for="tipo_documento" class="block text-sm font-medium text-slate-700 mb-1.5">
                Tipo de documento <span class="text-red-500">*</span>
            </label>
            <select id="tipo_documento" name="tipo_documento"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                    {{ $errors->has('tipo_documento') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                @foreach(['DNI', 'PASAPORTE', 'LC', 'LE', 'CI'] as $tipo)
                    <option value="{{ $tipo }}" {{ old('tipo_documento', $socio->tipo_documento ?? 'DNI') === $tipo ? 'selected' : '' }}>
                        {{ $tipo }}
                    </option>
                @endforeach
            </select>
            @error('tipo_documento')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Número de documento --}}
        <div>
            <label for="numero_documento" class="block text-sm font-medium text-slate-700 mb-1.5">
                Número de documento <span class="text-red-500">*</span>
            </label>
            <input type="text" id="numero_documento" name="numero_documento"
                value="{{ old('numero_documento', $socio->numero_documento ?? '') }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                    {{ $errors->has('numero_documento') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('numero_documento')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- ─────────────────────── Datos de contacto ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Contacto</h2>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email', $socio->email ?? '') }}"
                autocomplete="email" placeholder="socio@ejemplo.com"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                    {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="celular" class="block text-sm font-medium text-slate-700 mb-1.5">Celular</label>
            <input type="tel" id="celular" name="celular" value="{{ old('celular', $socio->celular ?? '') }}"
                placeholder="11 2345-6789"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                    {{ $errors->has('celular') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('celular')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono" class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono fijo</label>
            <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', $socio->telefono ?? '') }}"
                placeholder="011 4567-8901"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                    {{ $errors->has('telefono') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('telefono')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- ─────────────────────── Domicilio ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Domicilio</h2>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        <div class="sm:col-span-2 lg:col-span-2">
            <label for="direccion" class="block text-sm font-medium text-slate-700 mb-1.5">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $socio->direccion ?? '') }}"
                placeholder="Calle 123, Piso 4, Dpto B"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400">
        </div>

        <div>
            <label for="codigo_postal" class="block text-sm font-medium text-slate-700 mb-1.5">Código postal</label>
            <input type="text" id="codigo_postal" name="codigo_postal" value="{{ old('codigo_postal', $socio->codigo_postal ?? '') }}"
                placeholder="1234"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400">
        </div>

        <div>
            <label for="ciudad" class="block text-sm font-medium text-slate-700 mb-1.5">Ciudad / Localidad</label>
            <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad', $socio->ciudad ?? '') }}"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="sm:col-span-2">
            <label for="provincia" class="block text-sm font-medium text-slate-700 mb-1.5">Provincia</label>
            <select id="provincia" name="provincia"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                <option value="">Seleccionar…</option>
                @foreach($provincias as $prov)
                    <option value="{{ $prov }}" {{ old('provincia', $socio->provincia ?? '') === $prov ? 'selected' : '' }}>
                        {{ $prov }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>
</div>

{{-- ─────────────────────── Información del club ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Información del Club</h2>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        <div>
            <label for="categoria" class="block text-sm font-medium text-slate-700 mb-1.5">
                Categoría <span class="text-red-500">*</span>
            </label>
            <select id="categoria" name="categoria"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                    {{ $errors->has('categoria') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <option value="">Seleccionar…</option>
                <option value="adulto"   {{ old('categoria', $socio->categoria ?? '') === 'adulto'   ? 'selected' : '' }}>Adulto</option>
                <option value="junior"   {{ old('categoria', $socio->categoria ?? '') === 'junior'   ? 'selected' : '' }}>Junior</option>
                <option value="cadete"   {{ old('categoria', $socio->categoria ?? '') === 'cadete'   ? 'selected' : '' }}>Cadete</option>
                <option value="bebe"     {{ old('categoria', $socio->categoria ?? '') === 'bebe'     ? 'selected' : '' }}>Bebé</option>
                <option value="jubilado" {{ old('categoria', $socio->categoria ?? '') === 'jubilado' ? 'selected' : '' }}>Jubilado</option>
            </select>
            @error('categoria')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="estado" class="block text-sm font-medium text-slate-700 mb-1.5">
                Estado <span class="text-red-500">*</span>
            </label>
            <select id="estado" name="estado"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                    {{ $errors->has('estado') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <option value="pendiente"  {{ old('estado', $socio->estado ?? 'pendiente') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="activo"     {{ old('estado', $socio->estado ?? '') === 'activo'     ? 'selected' : '' }}>Activo</option>
                <option value="suspendido" {{ old('estado', $socio->estado ?? '') === 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                <option value="inactivo"   {{ old('estado', $socio->estado ?? '') === 'inactivo'   ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha_alta" class="block text-sm font-medium text-slate-700 mb-1.5">
                Fecha de alta <span class="text-red-500">*</span>
            </label>
            <input type="date" id="fecha_alta" name="fecha_alta"
                value="{{ old('fecha_alta', isset($socio) ? $socio->fecha_alta?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                    {{ $errors->has('fecha_alta') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('fecha_alta')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2 lg:col-span-3">
            <label class="block text-sm font-medium text-slate-700 mb-2">Cuota societaria</label>
            <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                <input type="hidden" name="paga_cuota_base" value="0">
                <input type="checkbox" name="paga_cuota_base" value="1"
                    {{ old('paga_cuota_base', isset($socio) ? ($socio->paga_cuota_base ? '1' : '0') : '1') === '1' ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                <span class="text-sm text-slate-700">Paga cuota societaria mensual</span>
            </label>
            <p class="text-xs text-slate-500 mt-1">Destildá si el socio solo abona disciplinas, sin cuota base.</p>
        </div>

        <div class="sm:col-span-2 lg:col-span-3">
            <label for="observaciones" class="block text-sm font-medium text-slate-700 mb-1.5">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="3"
                placeholder="Notas internas sobre el socio…"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400 resize-y">{{ old('observaciones', $socio->observaciones ?? '') }}</textarea>
            @error('observaciones')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>

{{-- ─────────────────────── Grupo familiar ─────────────────────── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Grupo Familiar</h2>
        <p class="text-xs text-slate-500 mt-0.5">Completá esta sección solo si el socio forma parte del grupo familiar de otro socio titular.</p>
    </div>
    <div class="p-6">

        @php
            $titularSeleccionado = old('socio_titular_id', $socio->socio_titular_id ?? $titularId ?? '');
        @endphp

        <div class="flex items-start gap-3 mb-5">
            <div class="flex items-center h-5 mt-0.5">
                <input type="checkbox" id="tiene_titular" name="tiene_titular" value="1"
                    {{ $titularSeleccionado ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
            </div>
            <div>
                <label for="tiene_titular" class="text-sm font-medium text-slate-700 cursor-pointer">
                    Este socio integra el grupo familiar de un titular
                </label>
                <p class="text-xs text-slate-500 mt-0.5">Al activar esta opción indicás de qué socio titular depende y el vínculo familiar.</p>
            </div>
        </div>

        <div id="grupo-familiar-campos" class="{{ $titularSeleccionado ? '' : 'hidden' }} grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">

            <div>
                <label for="socio_titular_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Socio titular <span class="text-red-500">*</span>
                </label>
                <select id="socio_titular_id" name="socio_titular_id"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                        {{ $errors->has('socio_titular_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    <option value="">Seleccionar titular…</option>
                    @foreach($titulares as $titular)
                        <option value="{{ $titular->id }}" {{ (string)$titularSeleccionado === (string)$titular->id ? 'selected' : '' }}>
                            N° {{ $titular->numero_socio }} — {{ $titular->nombreCompleto() }}
                        </option>
                    @endforeach
                </select>
                @error('socio_titular_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="parentesco" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Vínculo / Parentesco <span class="text-red-500">*</span>
                </label>
                <select id="parentesco" name="parentesco"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                        {{ $errors->has('parentesco') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    <option value="">Seleccionar vínculo…</option>
                    <option value="conyuge" {{ old('parentesco', $socio->parentesco ?? '') === 'conyuge' ? 'selected' : '' }}>Cónyuge / Pareja</option>
                    <option value="hijo"    {{ old('parentesco', $socio->parentesco ?? '') === 'hijo'    ? 'selected' : '' }}>Hijo/a</option>
                    <option value="padre"   {{ old('parentesco', $socio->parentesco ?? '') === 'padre'   ? 'selected' : '' }}>Padre / Madre</option>
                    <option value="hermano" {{ old('parentesco', $socio->parentesco ?? '') === 'hermano' ? 'selected' : '' }}>Hermano/a</option>
                    <option value="otro"    {{ old('parentesco', $socio->parentesco ?? '') === 'otro'    ? 'selected' : '' }}>Otro</option>
                </select>
                @error('parentesco')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>
</div>

<script>
    (function () {
        const input       = document.getElementById('foto');
        const preview     = document.getElementById('foto-preview');
        const placeholder = document.getElementById('foto-placeholder');

        if (input) {
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    if (placeholder) placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
    })();
</script>

<script>
    (function () {
        const checkbox = document.getElementById('tiene_titular');
        const campos   = document.getElementById('grupo-familiar-campos');
        const selectTitular   = document.getElementById('socio_titular_id');
        const selectParentesco = document.getElementById('parentesco');

        function toggle() {
            const activo = checkbox.checked;
            campos.classList.toggle('hidden', !activo);
            if (!activo) {
                selectTitular.value    = '';
                selectParentesco.value = '';
            }
        }

        checkbox.addEventListener('change', toggle);
    })();
</script>
