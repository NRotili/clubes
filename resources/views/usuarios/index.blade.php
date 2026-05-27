@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Usuarios del sistema</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            {{ $usuarios->total() }} {{ $usuarios->total() === 1 ? 'usuario' : 'usuarios' }} registrados
        </p>
    </div>
    <a href="{{ route('usuarios.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo Usuario
    </a>
</div>

<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Nombre</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Correo</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Rol</th>
                    <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 py-3">Socio vinculado</th>
                    <th class="px-4 py-3 w-24"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($usuarios as $usuario)
                    @php
                        $rolClases = [
                            'desarrollador'  => 'bg-violet-100 text-violet-700',
                            'administracion' => 'bg-blue-100 text-blue-700',
                            'socio'          => 'bg-slate-100 text-slate-600',
                        ];
                        $esYo = $usuario->id === auth()->id();
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ $esYo ? 'bg-blue-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-semibold text-slate-600">
                                        {{ mb_strtoupper(mb_substr($usuario->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-slate-900">{{ $usuario->name }}</span>
                                    @if($esYo)
                                        <span class="ml-2 text-xs text-blue-600 font-medium">(vos)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $usuario->email }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $rolClases[$usuario->rol] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ \App\Models\User::etiquetaRol($usuario->rol) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            @if($usuario->socio)
                                <a href="{{ route('socios.show', $usuario->socio) }}"
                                    class="text-blue-600 hover:underline font-medium">
                                    N° {{ $usuario->socio->numero_socio }} — {{ $usuario->socio->nombreCompleto() }}
                                </a>
                            @else
                                <span class="text-slate-400 text-xs">Sin vincular</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                    title="Editar"
                                    class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                    </svg>
                                </a>

                                @if(!$esYo)
                                    <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                                        onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($usuario->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            title="Eliminar"
                                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>

@endsection
