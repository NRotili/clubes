<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — {{ config('app.name', 'Club') }}</title>
    @if(\App\Models\ClubConfig::logoUrl())
        <link rel="icon" type="image/png" href="{{ \App\Models\ClubConfig::logoUrl() }}">
    @endif
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 antialiased font-sans">

    <div class="w-full max-w-sm">

        {{-- Logo / título --}}
        @php $logoUrl = \App\Models\ClubConfig::logoUrl(); $clubNombre = \App\Models\ClubConfig::nombre(); @endphp
        <div class="text-center mb-8">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $clubNombre }}" class="w-20 h-20 object-contain mx-auto mb-4">
            @else
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 shadow-lg mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                    </svg>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-slate-900">{{ $clubNombre }}</h1>
            <p class="text-slate-500 text-sm mt-1">Sistema de Gestión de Socios</p>
        </div>

        {{-- Tarjeta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Iniciar sesión</h2>

            {{-- Errores --}}
            @if($errors->any())
                <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Correo electrónico
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        autocomplete="email" autofocus required
                        class="w-full px-3.5 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                </div>

                {{-- Contraseña --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Contraseña
                    </label>
                    <input type="password" id="password" name="password"
                        autocomplete="current-password" required
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="remember" class="ml-2.5 text-sm text-slate-600 cursor-pointer">
                        Mantener sesión iniciada
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors shadow-sm">
                    Ingresar
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            {{ config('app.name', 'Club') }} &mdash; Sistema de Gestión
            &middot; <a href="{{ route('legal.privacidad') }}" class="hover:text-slate-600 hover:underline">Política de Privacidad</a>
        </p>
    </div>

</body>
</html>
