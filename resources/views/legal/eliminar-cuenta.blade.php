<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $clubNombre = \App\Models\ClubConfig::nombre();
        $logoUrl    = \App\Models\ClubConfig::logoUrl();
        $clubEmail  = \App\Models\ClubConfig::email();
    @endphp
    <title>Eliminar cuenta — {{ $clubNombre }}</title>
    @if($logoUrl)
        <link rel="icon" type="image/png" href="{{ $logoUrl }}">
    @endif
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <header class="bg-white border-b border-slate-200">
        <div class="max-w-2xl mx-auto px-6 py-6 flex items-center gap-4">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $clubNombre }}" class="w-12 h-12 object-contain shrink-0">
            @endif
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $clubNombre }}</p>
                <h1 class="text-xl font-bold text-slate-900">Solicitud de eliminación de cuenta</h1>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-6 py-10">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-4 text-sm mb-8 flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="text-sm text-slate-600 leading-relaxed mb-8 space-y-3">
            <p>
                Desde este formulario podés pedir la eliminación de tu cuenta de socio y de los datos personales
                asociados en {{ $clubNombre }} (sitio web y aplicación móvil), conforme a la Ley N° 25.326 de
                Protección de Datos Personales.
            </p>
            <p>
                Un administrador del club va a revisar tu pedido y confirmarte la baja. Tené en cuenta que, por
                obligaciones legales, contables e impositivas, es posible que debamos conservar algunos registros
                (por ejemplo, el historial de pagos) aunque tu cuenta y perfil dejen de estar activos. Podés leer más
                en nuestra <a href="{{ route('legal.privacidad') }}" class="text-blue-600 hover:underline">Política de Privacidad</a>.
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
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

            <form method="POST" action="{{ route('cuenta.eliminar.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nombre y apellido <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required
                        class="w-full px-3.5 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                            {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                </div>

                <div class="mb-4">
                    <label for="identificador" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Email, DNI o número de socio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="identificador" name="identificador" value="{{ old('identificador') }}" required
                        placeholder="Con el que te identificamos en el sistema"
                        class="w-full px-3.5 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400
                            {{ $errors->has('identificador') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                </div>

                <div class="mb-6">
                    <label for="motivo" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Motivo <span class="text-slate-400 font-normal">(opcional)</span>
                    </label>
                    <textarea id="motivo" name="motivo" rows="3"
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y">{{ old('motivo') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors shadow-sm">
                    Solicitar eliminación de mi cuenta
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-8">
            ¿Preferís escribirnos directamente? {{ $clubEmail ?: '[completar email de contacto]' }}
        </p>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">← Volver al inicio</a>
        </div>

    </main>

</body>
</html>
