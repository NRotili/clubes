@extends('layouts.app')

@section('title', 'Editar — ' . $usuario->name)

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('usuarios.index') }}" class="hover:text-slate-700 transition-colors">Usuarios</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">{{ $usuario->name }}</span>
</div>

<h1 class="text-2xl font-bold text-slate-900 mb-6">Editar usuario</h1>

@if($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
        </svg>
        <div>
            <p class="font-medium">Corregí los siguientes errores:</p>
            <ul class="list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('usuarios.update', $usuario) }}" novalidate>
    @csrf
    @method('PUT')

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Datos del usuario</h2>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Nombre completo <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Nueva contraseña
                    <span class="text-slate-400 font-normal">(dejar vacío para no cambiar)</span>
                </label>
                <input type="password" id="password" name="password" autocomplete="new-password"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                        {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Confirmar nueva contraseña
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label for="rol" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Rol <span class="text-red-500">*</span>
                </label>
                @if($usuario->id === auth()->id())
                    <input type="hidden" name="rol" value="{{ $usuario->rol }}">
                    <div class="px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50 text-slate-600">
                        {{ \App\Models\User::etiquetaRol($usuario->rol) }}
                        <span class="text-slate-400 text-xs ml-1">(no podés cambiar tu propio rol)</span>
                    </div>
                @else
                    <select id="rol" name="rol"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                            {{ $errors->has('rol') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                        <option value="administracion" {{ old('rol', $usuario->rol) === 'administracion' ? 'selected' : '' }}>Administración</option>
                        <option value="profesor"       {{ old('rol', $usuario->rol) === 'profesor'       ? 'selected' : '' }}>Profesor</option>
                        <option value="socio"          {{ old('rol', $usuario->rol) === 'socio'          ? 'selected' : '' }}>Socio</option>
                        <option value="desarrollador"  {{ old('rol', $usuario->rol) === 'desarrollador'  ? 'selected' : '' }}>Desarrollador</option>
                    </select>
                @endif
                @error('rol') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="profesor_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Profesor vinculado <span class="text-slate-400 font-normal">(opcional)</span>
                </label>
                <select id="profesor_id" name="profesor_id"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                        {{ $errors->has('profesor_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    <option value="">Sin vincular</option>
                    @foreach($profesores as $p)
                        <option value="{{ $p->id }}" {{ old('profesor_id', $usuario->profesor_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->nombreCompleto() }}
                        </option>
                    @endforeach
                </select>
                @error('profesor_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="socio_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Socio vinculado <span class="text-slate-400 font-normal">(opcional)</span>
                </label>
                <select id="socio_id" name="socio_id"
                    class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white
                        {{ $errors->has('socio_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    <option value="">Sin vincular</option>
                    @foreach($socios as $socio)
                        <option value="{{ $socio->id }}" {{ old('socio_id', $usuario->socio_id) == $socio->id ? 'selected' : '' }}>
                            N° {{ $socio->numero_socio }} — {{ $socio->nombreCompleto() }}
                        </option>
                    @endforeach
                </select>
                @error('socio_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('usuarios.index') }}"
            class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            Cancelar
        </a>
        <button type="submit"
            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            Guardar cambios
        </button>
    </div>
</form>

@endsection
