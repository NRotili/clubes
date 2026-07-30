<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ActividadTurnoController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AsistenciaDisciplinaController;
use App\Http\Controllers\ClubConfigController;
use App\Http\Controllers\ComunicacionController;
use App\Http\Controllers\CuotaConfigController;
use App\Http\Controllers\CuotaMensualController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeudoresController;
use App\Http\Controllers\DisciplinaController;
use App\Http\Controllers\EscanerController;
use App\Http\Controllers\FinanzasController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\MisClasesController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\SocioImportController;
use App\Http\Controllers\SolicitudEliminacionController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// ─── QR: verificación de acceso (pública, sin login) ─────────────────────────
Route::get('verificar/{uuid}', [SocioController::class, 'verificar'])->name('socios.verificar');

// ─── Legal (pública, sin login) ────────────────────────────────────────────────
Route::get('privacidad', [LegalController::class, 'privacidad'])->name('legal.privacidad');
Route::get('eliminar-cuenta', [SolicitudEliminacionController::class, 'create'])->name('cuenta.eliminar');
Route::post('eliminar-cuenta', [SolicitudEliminacionController::class, 'store'])->name('cuenta.eliminar.store');

// ─── Autenticación ────────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Raíz ─────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    if ($user->esSocio() && $user->socio_id) {
        return redirect()->route('socios.show', $user->socio);
    }

    return redirect()->route('dashboard');
});

// ─── Área protegida ───────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Manual de uso (todos los roles, cada uno ve el suyo) ──────────────────
    Route::get('manual', [ManualController::class, 'index'])->name('manual.index');

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::middleware('rol:administracion,desarrollador')->group(function () {
        Route::get('dashboard', [DashboardController::class,  'index'])->name('dashboard');
        Route::get('deudores', [DeudoresController::class,   'index'])->name('deudores.index');
        Route::get('asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
        Route::get('escaner', [EscanerController::class, 'index'])->name('escaner.index');
        Route::post('escaner/check', [EscanerController::class, 'check'])->name('escaner.check');
        Route::get('comunicaciones', [ComunicacionController::class, 'index'])->name('comunicaciones.index');
        Route::post('comunicaciones', [ComunicacionController::class, 'store'])->name('comunicaciones.store');
        Route::post('comunicaciones/socio/{socio}', [ComunicacionController::class, 'storeSocio'])->name('comunicaciones.socio');
        Route::get('noticias', [NoticiaController::class, 'index'])->name('noticias.index');
        Route::get('noticias/create', [NoticiaController::class, 'create'])->name('noticias.create');
        Route::post('noticias', [NoticiaController::class, 'store'])->name('noticias.store');
        Route::delete('noticias/{noticia}', [NoticiaController::class, 'destroy'])->name('noticias.destroy');
    });

    // ── Gestión de socios (administración + desarrollador) ────────────────────
    Route::middleware('rol:administracion,desarrollador')->group(function () {
        Route::get('socios', [SocioController::class, 'index'])->name('socios.index');
        Route::get('socios/create', [SocioController::class, 'create'])->name('socios.create');
        Route::post('socios', [SocioController::class, 'store'])->name('socios.store');

        // Importación masiva de socios desde Excel/CSV
        Route::get('socios/importar', [SocioImportController::class, 'create'])->name('socios.importar');
        Route::get('socios/importar/plantilla', [SocioImportController::class, 'plantilla'])->name('socios.importar.plantilla');
        Route::post('socios/importar/vista-previa', [SocioImportController::class, 'preview'])->name('socios.importar.preview');
        Route::post('socios/importar', [SocioImportController::class, 'store'])->name('socios.importar.store');
        Route::get('socios/{socio}/edit', [SocioController::class, 'edit'])->name('socios.edit');
        Route::put('socios/{socio}', [SocioController::class, 'update'])->name('socios.update');
        Route::delete('socios/{socio}', [SocioController::class, 'destroy'])->name('socios.destroy');
    });

    // Cualquier usuario autenticado puede ver el perfil de un socio
    // (el controller verifica que el rol "socio" solo vea el suyo)
    Route::get('socios/{socio}', [SocioController::class, 'show'])->name('socios.show');
    Route::get('socios/{socio}/qr', [SocioController::class, 'qr'])->name('socios.qr');

    Route::middleware('rol:administracion,desarrollador')->group(function () {

        // Papelera (restaurar)
        Route::get('papelera/socios', [SocioController::class, 'trash'])->name('socios.trash');
        Route::patch('papelera/socios/{uuid}/restaurar', [SocioController::class, 'restore'])->name('socios.restore');
    });

    // ── Disciplinas (administración + desarrollador) ──────────────────────────
    Route::middleware('rol:administracion,desarrollador')->group(function () {
        Route::get('disciplinas', [DisciplinaController::class, 'index'])->name('disciplinas.index');
        Route::get('disciplinas/create', [DisciplinaController::class, 'create'])->name('disciplinas.create');
        Route::get('disciplinas/calendario', [DisciplinaController::class, 'calendario'])->name('disciplinas.calendario');
        Route::post('disciplinas', [DisciplinaController::class, 'store'])->name('disciplinas.store');
        Route::get('disciplinas/{disciplina}', [DisciplinaController::class, 'show'])->name('disciplinas.show');
        Route::get('disciplinas/{disciplina}/edit', [DisciplinaController::class, 'edit'])->name('disciplinas.edit');
        Route::put('disciplinas/{disciplina}', [DisciplinaController::class, 'update'])->name('disciplinas.update');
        Route::delete('disciplinas/{disciplina}', [DisciplinaController::class, 'destroy'])->name('disciplinas.destroy');
        Route::post('disciplinas/{disciplina}/inscripciones', [DisciplinaController::class, 'inscribir'])->name('disciplinas.inscribir');
        Route::patch('disciplinas/{disciplina}/inscripciones/{socio}/baja', [DisciplinaController::class, 'darBaja'])->name('disciplinas.baja');
        Route::patch('disciplinas/{disciplina}/inscripciones/{socio}/reactivar', [DisciplinaController::class, 'reactivar'])->name('disciplinas.reactivar');
        Route::patch('disciplinas/{disciplina}/inscripciones/{socio}/beca', [DisciplinaController::class, 'toggleBeca'])->name('disciplinas.beca');
        Route::post('disciplinas/{disciplina}/profesores', [DisciplinaController::class, 'asignarProfesor'])->name('disciplinas.profesores.asignar');
        Route::delete('disciplinas/{disciplina}/profesores/{profesor}', [DisciplinaController::class, 'quitarProfesor'])->name('disciplinas.profesores.quitar');

        // Profesores
        Route::get('profesores', [ProfesorController::class, 'index'])->name('profesores.index');
        Route::get('profesores/create', [ProfesorController::class, 'create'])->name('profesores.create');
        Route::post('profesores', [ProfesorController::class, 'store'])->name('profesores.store');
        Route::get('profesores/{profesor}', [ProfesorController::class, 'show'])->name('profesores.show');
        Route::get('profesores/{profesor}/edit', [ProfesorController::class, 'edit'])->name('profesores.edit');
        Route::put('profesores/{profesor}', [ProfesorController::class, 'update'])->name('profesores.update');
        Route::delete('profesores/{profesor}', [ProfesorController::class, 'destroy'])->name('profesores.destroy');
    });

    // ── Actividades e instalaciones (administración + desarrollador) ──────────
    Route::middleware('rol:administracion,desarrollador')->group(function () {
        Route::get('actividades', [ActividadController::class, 'index'])->name('actividades.index');
        Route::get('actividades/create', [ActividadController::class, 'create'])->name('actividades.create');
        Route::post('actividades', [ActividadController::class, 'store'])->name('actividades.store');
        Route::get('actividades/solicitudes', [ActividadTurnoController::class, 'pendientes'])->name('actividades.turnos.pendientes');
        Route::get('actividades/{actividad}', [ActividadController::class, 'show'])->name('actividades.show');
        Route::get('actividades/{actividad}/edit', [ActividadController::class, 'edit'])->name('actividades.edit');
        Route::put('actividades/{actividad}', [ActividadController::class, 'update'])->name('actividades.update');
        Route::delete('actividades/{actividad}', [ActividadController::class, 'destroy'])->name('actividades.destroy');
        Route::get('actividades/{actividad}/agenda', [ActividadTurnoController::class, 'agenda'])->name('actividades.agenda');
        Route::post('actividades/{actividad}/turnos', [ActividadTurnoController::class, 'store'])->name('actividades.turnos.store');
        Route::patch('turnos/{turno}/aprobar', [ActividadTurnoController::class, 'aprobar'])->name('turnos.aprobar');
        Route::patch('turnos/{turno}/rechazar', [ActividadTurnoController::class, 'rechazar'])->name('turnos.rechazar');
        Route::patch('turnos/{turno}/cancelar', [ActividadTurnoController::class, 'cancelar'])->name('turnos.cancelar');
        Route::patch('turnos/{turno}/pagado', [ActividadTurnoController::class, 'marcarPagado'])->name('turnos.pagado');
    });

    // ── Cuotas mensuales y pagos ──────────────────────────────────────────────
    Route::middleware('rol:administracion,desarrollador')->group(function () {
        Route::get('cuotas', [CuotaMensualController::class, 'index'])->name('cuotas.index');
        Route::post('cuotas/generar', [CuotaMensualController::class, 'generar'])->name('cuotas.generar');
        Route::get('cuotas/{cuota}', [CuotaMensualController::class, 'show'])->name('cuotas.show');
        Route::patch('cuotas/{cuota}/ajustar-clases', [CuotaMensualController::class, 'ajustarClases'])->name('cuotas.ajustar-clases');
        Route::post('cuotas/{cuota}/recalcular', [CuotaMensualController::class, 'recalcular'])->name('cuotas.recalcular');

        Route::get('pagos/create', [PagoController::class, 'create'])->name('pagos.create');
        Route::post('pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::get('pagos/create-familiar', [PagoController::class, 'createFamiliar'])->name('pagos.create-familiar');
        Route::post('pagos/familiar', [PagoController::class, 'storeFamiliar'])->name('pagos.store-familiar');
        Route::get('pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
        Route::delete('pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    });

    // ── Finanzas ─────────────────────────────────────────────────────────────
    Route::middleware('rol:administracion,desarrollador')->group(function () {
        Route::get('finanzas', [FinanzasController::class, 'index'])->name('finanzas.index');
        Route::post('finanzas/egresos', [FinanzasController::class, 'storeEgreso'])->name('finanzas.egresos.store');
        Route::delete('finanzas/egresos/{egreso}', [FinanzasController::class, 'destroyEgreso'])->name('finanzas.egresos.destroy');
    });

    // ── Configuración de cuotas (administración + desarrollador) ─────────────
    Route::middleware('rol:administracion,desarrollador')->group(function () {
        Route::get('configuracion/cuotas', [CuotaConfigController::class, 'index'])->name('cuotas.config');
        Route::post('configuracion/cuotas', [CuotaConfigController::class, 'update'])->name('cuotas.config.update');
        Route::get('configuracion/club', [ClubConfigController::class, 'index'])->name('club.config');
        Route::post('configuracion/club', [ClubConfigController::class, 'update'])->name('club.config.update');

        Route::get('solicitudes-eliminacion', [SolicitudEliminacionController::class, 'index'])->name('solicitudes-eliminacion.index');
        Route::patch('solicitudes-eliminacion/{solicitud}/procesar', [SolicitudEliminacionController::class, 'procesar'])->name('solicitudes-eliminacion.procesar');
    });

    // ── Asistencia por disciplina (admin + profesor) ──────────────────────────
    Route::middleware('rol:administracion,desarrollador,profesor')->group(function () {
        Route::get('disciplinas/{disciplina}/asistencia', [AsistenciaDisciplinaController::class, 'planilla'])->name('disciplinas.asistencia.planilla');
        Route::get('disciplinas/{disciplina}/asistencia/tomar', [AsistenciaDisciplinaController::class, 'tomar'])->name('disciplinas.asistencia.tomar');
        Route::post('disciplinas/{disciplina}/asistencia', [AsistenciaDisciplinaController::class, 'store'])->name('disciplinas.asistencia.store');
    });

    // ── Profesor: mis clases (accesible a cualquier usuario con profesor vinculado) ──
    Route::get('mis-clases', [MisClasesController::class, 'index'])->name('profesor.mis-clases');

    // ── Solo desarrollador ────────────────────────────────────────────────────
    Route::middleware('rol:desarrollador')->group(function () {
        // Eliminación permanente de socios
        Route::delete('papelera/socios/{uuid}', [SocioController::class, 'forceDestroy'])->name('socios.force-destroy');

        // Gestión de usuarios
        Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        // Consola de comandos artisan
        Route::get('artisan', [ArtisanController::class, 'index'])->name('artisan.index');
        Route::post('artisan', [ArtisanController::class, 'run'])->name('artisan.run');
    });
});
