{{-- ─── Datos personales ─── --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Datos del profesor</h2>
    </div>
    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        <div>
            <label for="apellido" class="block text-sm font-medium text-slate-700 mb-1.5">
                Apellido <span class="text-red-500">*</span>
            </label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $profesor->apellido ?? '') }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                    {{ $errors->has('apellido') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('apellido')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1.5">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $profesor->nombre ?? '') }}"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                    {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('nombre')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="estado" class="block text-sm font-medium text-slate-700 mb-1.5">
                Estado <span class="text-red-500">*</span>
            </label>
            <select id="estado" name="estado"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white
                    {{ $errors->has('estado') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                <option value="activo"   {{ old('estado', $profesor->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $profesor->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email', $profesor->email ?? '') }}"
                placeholder="profesor@ejemplo.com"
                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400
                    {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="celular" class="block text-sm font-medium text-slate-700 mb-1.5">Celular</label>
            <input type="tel" id="celular" name="celular" value="{{ old('celular', $profesor->celular ?? '') }}"
                placeholder="11 2345-6789"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
        </div>

        <div>
            <label for="telefono" class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono fijo</label>
            <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', $profesor->telefono ?? '') }}"
                placeholder="011 4567-8901"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
        </div>

        <div>
            <label for="cuil" class="block text-sm font-medium text-slate-700 mb-1.5">CUIL</label>
            <input type="text" id="cuil" name="cuil" value="{{ old('cuil', $profesor->cuil ?? '') }}"
                placeholder="20-12345678-9"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
        </div>

        <div class="sm:col-span-2">
            <label for="observaciones" class="block text-sm font-medium text-slate-700 mb-1.5">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="2"
                class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y placeholder-slate-400"
                placeholder="Especialidades, notas…">{{ old('observaciones', $profesor->observaciones ?? '') }}</textarea>
        </div>

    </div>
</div>
