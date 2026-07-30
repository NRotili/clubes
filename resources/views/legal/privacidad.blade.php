<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $clubNombre  = \App\Models\ClubConfig::nombre();
        $logoUrl     = \App\Models\ClubConfig::logoUrl();
        $clubEmail   = \App\Models\ClubConfig::email();
        $clubTel     = \App\Models\ClubConfig::telefono();
        $clubDir     = \App\Models\ClubConfig::direccion();
    @endphp
    <title>Política de Privacidad — {{ $clubNombre }}</title>
    @if($logoUrl)
        <link rel="icon" type="image/png" href="{{ $logoUrl }}">
    @endif
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <header class="bg-white border-b border-slate-200">
        <div class="max-w-3xl mx-auto px-6 py-6 flex items-center gap-4">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $clubNombre }}" class="w-12 h-12 object-contain shrink-0">
            @endif
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $clubNombre }}</p>
                <h1 class="text-xl font-bold text-slate-900">Política de Privacidad</h1>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-10">

        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-4 py-3 text-sm mb-10">
            Última actualización: {{ now()->translatedFormat('d \d\e F \d\e Y') }}. Esta política aplica tanto al sitio web
            de gestión de socios como a la aplicación móvil de {{ $clubNombre }}.
        </div>

        <div class="prose prose-slate max-w-none prose-h2:text-lg prose-h2:font-bold prose-h2:text-slate-900 prose-h2:mt-10 prose-h2:mb-3
                    prose-h3:text-sm prose-h3:font-semibold prose-h3:text-slate-700 prose-h3:mt-5 prose-h3:mb-2
                    prose-p:text-sm prose-p:text-slate-600 prose-p:leading-relaxed
                    prose-li:text-sm prose-li:text-slate-600 prose-ul:my-2">

            <p>
                En {{ $clubNombre }} respetamos tu privacidad y nos comprometemos a proteger los datos personales de
                nuestros socios, familiares a cargo, profesores y usuarios de nuestra aplicación móvil. Esta política
                explica qué información recopilamos, para qué la usamos, con quién la compartimos y qué derechos tenés
                sobre tus datos, en línea con la Ley N° 25.326 de Protección de Datos Personales de la República Argentina.

            </p>

            <h2>1. Responsable del tratamiento de los datos</h2>
            <p>
                El responsable de los datos personales recopilados a través del sitio web y la aplicación móvil es
                <strong>{{ $clubNombre }}</strong>.
            </p>
            <ul>
                <li>Dirección: {{ $clubDir ?: '[completar dirección del club]' }}</li>
                <li>Correo electrónico de contacto: {{ $clubEmail ?: '[completar email de contacto]' }}</li>
                <li>Teléfono: {{ $clubTel ?: '[completar teléfono de contacto]' }}</li>
            </ul>

            <h2>2. Qué información recopilamos</h2>

            <h3>2.1. Datos de identificación y contacto</h3>
            <p>Al asociarte o crear tu cuenta, recopilamos: nombre y apellido, tipo y número de documento, fecha de
            nacimiento, género, correo electrónico, teléfono y celular, dirección, ciudad, provincia y código postal,
            y una fotografía de perfil (opcional). También registramos el vínculo familiar cuando un socio forma parte
            del grupo familiar de otro (parentesco y socio titular).</p>

            <h3>2.2. Datos de la cuenta de acceso</h3>
            <p>Correo electrónico y contraseña (almacenada siempre encriptada, nunca en texto plano). En la app móvil,
            si activás el desbloqueo por huella dactilar o reconocimiento facial, esa verificación biométrica la procesa
            el sistema operativo de tu propio celular — nunca se envía ni se almacena en nuestros servidores.</p>

            <h3>2.3. Datos de actividad en el club</h3>
            <ul>
                <li>Inscripciones a disciplinas/actividades, becas y asistencia a clases.</li>
                <li>Reservas de instalaciones y turnos.</li>
                <li>Registro de ingresos al club (fecha y hora de acceso mediante el código QR de tu carnet).</li>
                <li>Historial de cuotas, pagos y método de pago utilizado (no almacenamos datos completos de
                tarjetas de crédito o débito).</li>
            </ul>

            <h3>2.4. Datos técnicos y de la aplicación móvil</h3>
            <ul>
                <li>Token de notificaciones push (para enviarte avisos de vencimientos, novedades y confirmaciones de
                pago). Se genera a través del servicio de notificaciones de Expo/Firebase Cloud Messaging.</li>
                <li>Datos de sesión (cookies) en el sitio web, necesarios para mantener tu inicio de sesión activo.</li>
            </ul>

            <h3>2.5. Datos de menores de edad</h3>
            <p>
                Como club, gestionamos categorías de socios que incluyen menores de edad (bebé, cadete, junior). Los
                datos de un socio menor de edad son cargados y administrados por su madre, padre o tutor/a, o por el
                club con el consentimiento de estos. No recopilamos directamente datos de menores a través de la
                aplicación móvil: el acceso a la cuenta de un socio menor requiere que un adulto responsable la
                gestione. Si creés que recopilamos datos de un menor sin el consentimiento correspondiente, contactanos
                para eliminarlos de inmediato.
            </p>

            <h2>3. Para qué usamos tus datos</h2>
            <ul>
                <li>Gestionar tu condición de socio: alta, cuotas, pagos, y verificación de acceso al club.</li>
                <li>Administrar tu inscripción y asistencia a disciplinas y actividades.</li>
                <li>Enviarte comunicaciones y notificaciones relacionadas con tu cuenta (vencimientos, novedades del
                club, confirmaciones de pago o de turnos).</li>
                <li>Generar tu carnet digital y código QR de acceso.</li>
                <li>Cumplir con obligaciones legales, contables e impositivas del club.</li>
                <li>Mejorar el funcionamiento del sistema y resolver problemas técnicos.</li>
            </ul>
            <p>No utilizamos tus datos personales con fines de publicidad de terceros ni los vendemos a otras
            empresas.</p>

            <h2>4. Con quién compartimos tu información</h2>
            <p>No compartimos tus datos personales con terceros para fines comerciales. Solo los compartimos en estos
            casos puntuales:</p>
            <ul>
                <li><strong>Proveedores de infraestructura técnica</strong>: utilizamos el servicio de notificaciones
                push de Expo, que a su vez utiliza Firebase Cloud Messaging (Google) para Android, únicamente para
                entregarte las notificaciones de la app. Estos proveedores no reciben tu información personal
                completa, solo el identificador técnico necesario para enviarte el mensaje.</li>
                <li><strong>Obligación legal</strong>: si una autoridad competente lo requiere conforme a la ley
                argentina.</li>
                <li><strong>Dentro del club</strong>: el personal administrativo del club accede a tus datos
                únicamente en la medida necesaria para gestionar tu cuenta, cuotas y actividades.</li>
            </ul>

            <h2>5. Cómo protegemos tu información</h2>
            <ul>
                <li>Las contraseñas se almacenan encriptadas (hash), nunca en texto plano.</li>
                <li>Las comunicaciones entre la app/sitio web y nuestros servidores viajan cifradas (HTTPS).</li>
                <li>El acceso administrativo a los datos está restringido por roles: solo el personal autorizado
                puede ver o modificar información de socios.</li>
                <li>Los tokens de sesión de la app móvil se guardan de forma segura en el almacenamiento cifrado del
                propio dispositivo (Secure Store).</li>
            </ul>

            <h2>6. Cuánto tiempo conservamos tus datos</h2>
            <p>
                Conservamos tus datos personales mientras mantengas tu condición de socio y durante el plazo adicional
                que exijan las obligaciones legales, contables e impositivas aplicables (por ejemplo, los registros de
                pagos). Si dejás de ser socio, tus datos pueden conservarse en un archivo histórico con acceso
                restringido, salvo que solicites su eliminación conforme a la sección siguiente.
            </p>

            <h2>7. Tus derechos sobre tus datos</h2>
            <p>De acuerdo con la Ley N° 25.326, tenés derecho a:</p>
            <ul>
                <li><strong>Acceder</strong> a los datos personales que tenemos sobre vos.</li>
                <li><strong>Rectificar</strong> datos inexactos o desactualizados.</li>
                <li><strong>Actualizar</strong> tu información de contacto en cualquier momento desde tu perfil.</li>
                <li><strong>Suprimir</strong> tus datos cuando ya no sean necesarios para los fines por los que fueron
                recolectados, salvo obligación legal de conservarlos.</li>
                <li><strong>Revocar tu consentimiento</strong> para el envío de notificaciones, desde la configuración
                de la app.</li>
            </ul>
            <p>
                Para ejercer estos derechos, escribinos a {{ $clubEmail ?: '[completar email de contacto]' }}.
                Si específicamente querés pedir la <strong>eliminación de tu cuenta y tus datos</strong>, podés hacerlo
                desde nuestro <a href="{{ route('cuenta.eliminar') }}" class="text-blue-600 hover:underline">formulario de
                solicitud de eliminación de cuenta</a>. La Agencia de Acceso a la Información Pública (AAIP), órgano de
                control de la Ley N° 25.326, es la autoridad ante la cual también podés efectuar reclamos en caso de
                considerar vulnerados tus derechos.
            </p>

            <h2>8. Notificaciones push y biometría en la app móvil</h2>
            <p>
                Podés desactivar las notificaciones push en cualquier momento desde la configuración de tu celular.
                El desbloqueo por huella o Face ID es opcional y se puede activar o desactivar desde tu perfil dentro
                de la app; como se mencionó, esa verificación ocurre enteramente en tu dispositivo y nunca se transmite
                a nuestros servidores.
            </p>

            <h2>9. Cookies</h2>
            <p>
                El sitio web utiliza únicamente cookies técnicas necesarias para mantener tu sesión iniciada. No
                utilizamos cookies de seguimiento publicitario ni de terceros con fines analíticos.
            </p>

            <h2>10. Cambios a esta política</h2>
            <p>
                Podemos actualizar esta política ocasionalmente para reflejar cambios en nuestras prácticas o en la
                normativa vigente. Publicaremos cualquier cambio en esta misma página, indicando la fecha de la última
                actualización.
            </p>

            <h2>11. Contacto</h2>
            <p>
                Ante cualquier consulta sobre esta política o sobre el tratamiento de tus datos personales, podés
                escribirnos a {{ $clubEmail ?: '[completar email de contacto]' }}
                @if($clubTel) o llamarnos al {{ $clubTel }} @endif.
            </p>

        </div>

        <div class="mt-12 pt-6 border-t border-slate-200 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">← Volver al inicio</a>
        </div>

    </main>

</body>
</html>
