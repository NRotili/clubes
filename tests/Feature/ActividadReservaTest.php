<?php

namespace Tests\Feature;

use App\Models\Actividad;
use App\Models\ActividadTurno;
use App\Models\Socio;
use App\Models\User;
use App\Services\ActividadDisponibilidadService;
use App\Services\ActividadReservaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ActividadReservaTest extends TestCase
{
    use RefreshDatabase;

    private function crearSocio(string $numero): Socio
    {
        return Socio::create([
            'numero_socio'     => $numero,
            'nombre'           => 'Socio',
            'apellido'         => $numero,
            'numero_documento' => '3000000' . $numero,
            'fecha_nacimiento' => '1990-01-01',
            'genero'           => 'M',
            'fecha_alta'       => now()->toDateString(),
            'estado'           => 'activo',
        ]);
    }

    private function crearActividad(array $overrides = []): Actividad
    {
        return Actividad::create(array_merge([
            'nombre' => 'Cancha de Tenis',
            'estado' => 'activa',
        ], $overrides));
    }

    public function test_genera_slots_segun_franja_y_ocupacion(): void
    {
        $actividad = $this->crearActividad();
        $fecha     = Carbon::parse('next monday');

        $actividad->franjas()->create([
            'dia_semana'       => 'lunes',
            'hora_inicio'      => '08:00',
            'hora_fin'         => '10:00',
            'duracion_minutos' => 60,
            'cupo'             => 3,
        ]);

        $slots = ActividadDisponibilidadService::slots($actividad, $fecha);

        $this->assertCount(2, $slots);
        $this->assertSame('08:00', $slots[0]['hora_inicio']);
        $this->assertSame('09:00', $slots[0]['hora_fin']);
        $this->assertSame(3, $slots[0]['cupo']);
        $this->assertSame(0, $slots[0]['ocupados']);
        $this->assertSame(3, $slots[0]['disponibles']);

        $socio = $this->crearSocio('0001');
        ActividadReservaService::reservar($actividad, $socio, $fecha->format('Y-m-d'), '08:00');

        $slots = ActividadDisponibilidadService::slots($actividad, $fecha);
        $this->assertSame(1, $slots[0]['ocupados']);
        $this->assertSame(2, $slots[0]['disponibles']);
    }

    public function test_reserva_exitosa_se_confirma_automaticamente(): void
    {
        $actividad = $this->crearActividad();
        $actividad->franjas()->create([
            'dia_semana'       => 'lunes',
            'hora_inicio'      => '08:00',
            'hora_fin'         => '10:00',
            'duracion_minutos' => 60,
            'cupo'             => 3,
        ]);

        $socio = $this->crearSocio('0001');
        $fecha = Carbon::parse('next monday');

        $turno = ActividadReservaService::reservar($actividad, $socio, $fecha->format('Y-m-d'), '08:00');

        $this->assertSame('confirmado', $turno->estado);
        $this->assertDatabaseHas('actividad_turnos', [
            'id'         => $turno->id,
            'actividad_id' => $actividad->id,
            'socio_id'   => $socio->id,
            'fecha'      => $fecha->format('Y-m-d'),
            'hora_inicio' => '08:00',
            'estado'     => 'confirmado',
        ]);
    }

    public function test_reserva_sin_cupo_lanza_excepcion(): void
    {
        $actividad = $this->crearActividad();
        $actividad->franjas()->create([
            'dia_semana'       => 'lunes',
            'hora_inicio'      => '08:00',
            'hora_fin'         => '10:00',
            'duracion_minutos' => 60,
            'cupo'             => 1,
        ]);

        $fecha  = Carbon::parse('next monday');
        $socio1 = $this->crearSocio('0001');
        $socio2 = $this->crearSocio('0002');

        ActividadReservaService::reservar($actividad, $socio1, $fecha->format('Y-m-d'), '08:00');

        $this->expectException(ValidationException::class);
        ActividadReservaService::reservar($actividad, $socio2, $fecha->format('Y-m-d'), '08:00');
    }

    public function test_reserva_con_aprobacion_queda_pendiente(): void
    {
        $actividad = $this->crearActividad(['requiere_aprobacion' => true]);
        $actividad->franjas()->create([
            'dia_semana'       => 'lunes',
            'hora_inicio'      => '08:00',
            'hora_fin'         => '10:00',
            'duracion_minutos' => 60,
            'cupo'             => 3,
        ]);

        $socio = $this->crearSocio('0001');
        $fecha = Carbon::parse('next monday');

        $turno = ActividadReservaService::reservar($actividad, $socio, $fecha->format('Y-m-d'), '08:00');

        $this->assertSame('pendiente', $turno->estado);
    }

    public function test_admin_puede_aprobar_y_rechazar_turnos_pendientes(): void
    {
        $actividad = $this->crearActividad(['requiere_aprobacion' => true]);
        $actividad->franjas()->create([
            'dia_semana'       => 'lunes',
            'hora_inicio'      => '08:00',
            'hora_fin'         => '10:00',
            'duracion_minutos' => 60,
            'cupo'             => 3,
        ]);

        $fecha  = Carbon::parse('next monday');
        $socio1 = $this->crearSocio('0001');
        $socio2 = $this->crearSocio('0002');

        $turnoAprobar  = ActividadReservaService::reservar($actividad, $socio1, $fecha->format('Y-m-d'), '08:00');
        $turnoRechazar = ActividadReservaService::reservar($actividad, $socio2, $fecha->format('Y-m-d'), '09:00');

        $admin = User::factory()->create(['rol' => 'administracion']);

        $this->actingAs($admin)
            ->patch(route('turnos.aprobar', $turnoAprobar))
            ->assertRedirect();

        $this->actingAs($admin)
            ->patch(route('turnos.rechazar', $turnoRechazar))
            ->assertRedirect();

        $this->assertSame('confirmado', $turnoAprobar->fresh()->estado);
        $this->assertSame('rechazado', $turnoRechazar->fresh()->estado);
    }

    public function test_socio_puede_cancelar_turno_futuro(): void
    {
        $actividad = $this->crearActividad();
        $actividad->franjas()->create([
            'dia_semana'       => 'lunes',
            'hora_inicio'      => '08:00',
            'hora_fin'         => '10:00',
            'duracion_minutos' => 60,
            'cupo'             => 3,
        ]);

        $socio = $this->crearSocio('0001');
        $fecha = Carbon::parse('next monday');

        $turno = ActividadReservaService::reservar($actividad, $socio, $fecha->format('Y-m-d'), '08:00');

        $this->assertTrue($turno->puedeCancelar());

        $admin = User::factory()->create(['rol' => 'administracion']);

        $this->actingAs($admin)
            ->patch(route('turnos.cancelar', $turno))
            ->assertRedirect();

        $turno->refresh();
        $this->assertSame('cancelado', $turno->estado);
        $this->assertFalse($turno->puedeCancelar());
    }
}
