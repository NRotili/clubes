@extends('layouts.app')
@section('title', 'Tablón de noticias')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Tablón de noticias</h1>
        <p class="text-sm text-slate-500 mt-0.5">Las noticias publicadas se envían como notificación a todos los socios con la app.</p>
    </div>
    <a href="{{ route('noticias.create') }}"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nueva noticia
    </a>
</div>

@if($noticias->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
        </svg>
        <p class="text-slate-500 font-medium">No hay noticias publicadas todavía.</p>
        <a href="{{ route('noticias.create') }}" class="inline-block mt-3 text-sm text-blue-600 font-medium hover:underline">
            Publicar la primera noticia →
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($noticias as $noticia)
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">{{ $noticia->titulo }}</h2>
                        <div class="prose prose-sm prose-slate mt-2 max-w-none">{!! $noticia->cuerpo !!}</div>
                    </div>
                    <form method="POST" action="{{ route('noticias.destroy', $noticia) }}"
                        onsubmit="return confirm('¿Eliminar esta noticia?')" class="shrink-0">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors"
                            title="Eliminar">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </button>
                    </form>
                </div>
                <div class="flex items-center gap-3 mt-4 pt-3 border-t border-slate-100 text-xs text-slate-400">
                    <span>{{ $noticia->created_at->isoFormat('D [de] MMMM [de] YYYY, H:mm') }}</span>
                    <span>·</span>
                    <span>Por {{ $noticia->autor->name }}</span>
                </div>
            </div>
        @endforeach
    </div>

    @if($noticias->hasPages())
        <div class="mt-6">{{ $noticias->links() }}</div>
    @endif
@endif

@endsection
