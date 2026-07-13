@extends('layouts.app')
@section('title', 'Manual de uso')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Manual de uso</h1>
    <p class="text-sm text-slate-500 mt-0.5">Guía paso a paso de las funciones disponibles para tu perfil ({{ \App\Models\User::etiquetaRol(auth()->user()->rol) }}).</p>
</div>

<div class="space-y-3">
    @include($vista)
</div>

@endsection
