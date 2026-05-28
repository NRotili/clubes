<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') — {{ config('app.name', 'Club') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

@auth
<div class="flex min-h-screen">

    {{-- ─── Sidebar desktop ─── --}}
    <aside class="hidden lg:flex lg:flex-col w-60 bg-slate-900 text-white fixed inset-y-0 left-0 z-40">

        {{-- Logo --}}
        <div class="px-5 py-4 border-b border-slate-800">
            <a href="{{ route('socios.index') }}" class="flex items-center gap-3 font-bold text-white text-base">
                @if($club['logo_url'])
                    <img src="{{ $club['logo_url'] }}" alt="Logo" class="w-8 h-8 object-contain rounded shrink-0">
                @else
                    <svg class="w-7 h-7 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                    </svg>
                @endif
                <span class="truncate">{{ $club['nombre'] }}</span>
            </a>
        </div>

        {{-- Nav groups --}}
        <nav class="flex-1 px-3 py-5 space-y-6 overflow-y-auto [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:bg-slate-900 [&::-webkit-scrollbar-thumb]:bg-slate-600 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-slate-500">

            @if(auth()->user()->puedeGestionarSocios())
            {{-- Dashboard --}}
            <div>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 mb-1.5">Socios</p>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('socios.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('socios.index', 'socios.create', 'socios.edit', 'socios.show', 'socios.store', 'socios.update', 'socios.destroy') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                            </svg>
                            Socios
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('escaner.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('escaner.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5ZM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5Z"/>
                            </svg>
                            Escáner QR
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('asistencia.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('asistencia.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5ZM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5Z"/>
                            </svg>
                            Asistencia QR
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('socios.trash') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('socios.trash') ? 'bg-amber-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                            Papelera
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 mb-1.5">Actividades</p>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('disciplinas.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('disciplinas.index', 'disciplinas.create', 'disciplinas.show', 'disciplinas.edit') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                            </svg>
                            Disciplinas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('disciplinas.calendario') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('disciplinas.calendario') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                            </svg>
                            Calendario
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profesores.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('profesores.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                            </svg>
                            Profesores
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 mb-1.5">Comunicación</p>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('comunicaciones.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('comunicaciones.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                            </svg>
                            Comunicaciones
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('noticias.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('noticias.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
                            </svg>
                            Tablón de noticias
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 mb-1.5">Finanzas</p>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('deudores.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('deudores.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                            </svg>
                            Deudores
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('finanzas.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('finanzas.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z"/>
                            </svg>
                            Ingresos y egresos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cuotas.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('cuotas.index', 'cuotas.show', 'pagos.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/>
                            </svg>
                            Cobros
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            @if(auth()->user()->puedeGestionarSocios())
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-3 mb-1.5">Configuración</p>
                <ul class="space-y-0.5">
                    <li>
                        <a href="{{ route('club.config') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('club.config*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253M3 12c0 .778.099 1.533.284 2.253"/>
                            </svg>
                            Datos del club
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cuotas.config') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('cuotas.config*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            Cuotas y mora
                        </a>
                    </li>
                    @if(auth()->user()->esDesarrollador())
                    <li>
                        <a href="{{ route('usuarios.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('usuarios.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            Usuarios
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            @endif

        </nav>

        {{-- Usuario + cerrar sesión --}}
        <div class="px-4 py-4 border-t border-slate-800">
            @php
                $rolClasesSidebar = [
                    'desarrollador'  => 'bg-violet-500/20 text-violet-300',
                    'administracion' => 'bg-blue-500/20 text-blue-300',
                    'socio'          => 'bg-slate-600 text-slate-300',
                ];
            @endphp
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-sm font-semibold text-white shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                    <span class="inline-block text-xs font-medium px-1.5 py-0.5 rounded mt-0.5 {{ $rolClasesSidebar[auth()->user()->rol] ?? 'bg-slate-600 text-slate-300' }}">
                        {{ \App\Models\User::etiquetaRol(auth()->user()->rol) }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar sesión"
                        class="p-1.5 text-slate-500 hover:text-white hover:bg-slate-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ─── Columna derecha: header mobile + contenido ─── --}}
    <div class="flex-1 flex flex-col min-h-screen lg:ml-60">

        {{-- Header mobile (hamburguesa) --}}
        <header class="lg:hidden bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="px-4 flex items-center h-14 gap-3">
                <a href="{{ route('socios.index') }}" class="flex items-center gap-2 font-semibold text-slate-900 flex-1">
                    @if($club['logo_url'])
                        <img src="{{ $club['logo_url'] }}" alt="Logo" class="w-6 h-6 object-contain shrink-0">
                    @else
                        <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                        </svg>
                    @endif
                    {{ $club['nombre'] }}
                </a>
                <button id="menu-toggle" type="button"
                    class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors"
                    aria-label="Abrir menú">
                    <svg id="icon-menu" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                    <svg id="icon-close" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Menú móvil desplegable --}}
            <div id="mobile-menu" class="hidden border-t border-slate-100 bg-white">
                <nav class="px-4 py-3 flex flex-col gap-1">
                    <div class="px-3 py-2 mb-1 border-b border-slate-100">
                        <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        <span class="text-xs text-slate-500">{{ \App\Models\User::etiquetaRol(auth()->user()->rol) }}</span>
                    </div>

                    @if(auth()->user()->puedeGestionarSocios())
                        <a href="{{ route('socios.index') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('socios.index', 'socios.create', 'socios.edit', 'socios.show') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Socios
                        </a>
                        <a href="{{ route('socios.trash') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('socios.trash') ? 'bg-amber-50 text-amber-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Papelera
                        </a>
                        <a href="{{ route('disciplinas.index') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('disciplinas.index', 'disciplinas.create', 'disciplinas.show', 'disciplinas.edit') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Disciplinas
                        </a>
                        <a href="{{ route('disciplinas.calendario') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('disciplinas.calendario') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Calendario
                        </a>
                        <a href="{{ route('profesores.index') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('profesores.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Profesores
                        </a>
                        <a href="{{ route('finanzas.index') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('finanzas.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Ingresos y egresos
                        </a>
                        <a href="{{ route('cuotas.index') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('cuotas.index', 'cuotas.show', 'pagos.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Cobros
                        </a>
                        <a href="{{ route('cuotas.config') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('cuotas.config*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Configurar cuotas
                        </a>
                        <a href="{{ route('noticias.index') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('noticias.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Tablón de noticias
                        </a>
                    @endif
                    @if(auth()->user()->esDesarrollador())
                        <a href="{{ route('usuarios.index') }}"
                            class="px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('usuarios.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            Usuarios
                        </a>
                    @endif

                    <div class="mt-2 pt-2 border-t border-slate-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-3 py-2.5 rounded-md text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </header>

        {{-- Contenido principal --}}
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

            @if(session('success'))
                <div class="mb-6 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-slate-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-xs text-slate-400 text-center">
                {{ config('app.name', 'Club') }} &mdash; Sistema de Gestión de Socios
            </div>
        </footer>

    </div>

</div>

@else

{{-- Layout para invitados (login, verificar QR, etc.) --}}
<div class="min-h-screen flex flex-col">
    <main class="flex-1">
        @yield('content')
    </main>
</div>

@endauth

<script>
(function () {
    const toggle    = document.getElementById('menu-toggle');
    const menu      = document.getElementById('mobile-menu');
    const iconMenu  = document.getElementById('icon-menu');
    const iconClose = document.getElementById('icon-close');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
        const open = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', open);
        iconMenu.classList.toggle('hidden', !open);
        iconClose.classList.toggle('hidden', open);
    });
})();
</script>

</body>
</html>
