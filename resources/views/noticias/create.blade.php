@extends('layouts.app')
@section('title', 'Nueva noticia')

@section('content')

<div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
    <a href="{{ route('noticias.index') }}" class="hover:text-slate-700 transition-colors">Tablón de noticias</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
    </svg>
    <span class="text-slate-900 font-medium">Nueva noticia</span>
</div>

<div>
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">

        <form method="POST" action="{{ route('noticias.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="titulo" class="block text-sm font-medium text-slate-700 mb-1.5">Título</label>
                <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('titulo') border-red-400 @enderror"
                    placeholder="Título de la noticia">
                @error('titulo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Contenido</label>
                <input type="hidden" id="cuerpo" name="cuerpo" value="{{ old('cuerpo') }}">
                <div id="quill-editor"></div>
                @error('cuerpo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                    </svg>
                    Publicar y notificar
                </button>
                <a href="{{ route('noticias.index') }}"
                    class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <p class="mt-4 text-xs text-slate-400">
        Al publicar, se enviará una notificación push a todos los socios que tengan la app instalada.
    </p>
</div>

@endsection
