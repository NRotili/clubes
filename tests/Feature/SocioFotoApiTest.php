<?php

namespace Tests\Feature;

use App\Models\Socio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SocioFotoApiTest extends TestCase
{
    use RefreshDatabase;

    private function crearSocioConUsuario(): array
    {
        $socio = Socio::create([
            'numero_socio'     => Socio::generarNumeroSocio(),
            'qr_uuid'          => Socio::generarQrUuid(),
            'apellido'         => 'Test',
            'nombre'           => 'Foto',
            'tipo_documento'   => 'DNI',
            'numero_documento' => '12345678',
            'fecha_nacimiento' => '1990-01-01',
            'genero'           => 'M',
            'categoria'        => 'adulto',
            'estado'           => 'activo',
            'fecha_alta'       => now(),
        ]);

        $user = User::create([
            'name'     => 'Foto Test',
            'email'    => 'fototest@test.com',
            'password' => bcrypt('password'),
            'rol'      => 'socio',
            'socio_id' => $socio->id,
        ]);

        return [$socio, $user];
    }

    public function test_socio_puede_subir_su_foto_de_perfil(): void
    {
        Storage::fake('public');
        [$socio, $user] = $this->crearSocioConUsuario();

        $foto = UploadedFile::fake()->image('perfil.jpg', 400, 400);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/me/foto', ['foto' => $foto]);

        $response->assertOk();
        $response->assertJsonStructure(['message', 'foto_url']);

        $socio->refresh();
        $this->assertNotNull($socio->foto);
        Storage::disk('public')->assertExists($socio->foto);
    }

    public function test_subir_una_foto_nueva_borra_la_anterior(): void
    {
        Storage::fake('public');
        [$socio, $user] = $this->crearSocioConUsuario();

        $primera = UploadedFile::fake()->image('primera.jpg', 400, 400);
        $this->actingAs($user, 'sanctum')->postJson('/api/me/foto', ['foto' => $primera]);
        $socio->refresh();
        $rutaPrimera = $socio->foto;

        $segunda = UploadedFile::fake()->image('segunda.jpg', 400, 400);
        $this->actingAs($user, 'sanctum')->postJson('/api/me/foto', ['foto' => $segunda]);
        $socio->refresh();

        Storage::disk('public')->assertMissing($rutaPrimera);
        Storage::disk('public')->assertExists($socio->foto);
        $this->assertNotEquals($rutaPrimera, $socio->foto);
    }

    public function test_socio_puede_eliminar_su_foto(): void
    {
        Storage::fake('public');
        [$socio, $user] = $this->crearSocioConUsuario();

        $foto = UploadedFile::fake()->image('perfil.jpg', 400, 400);
        $this->actingAs($user, 'sanctum')->postJson('/api/me/foto', ['foto' => $foto]);
        $socio->refresh();
        $ruta = $socio->foto;

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/me/foto');

        $response->assertOk();
        $socio->refresh();
        $this->assertNull($socio->foto);
        Storage::disk('public')->assertMissing($ruta);
    }

    public function test_rechaza_archivo_que_no_es_imagen(): void
    {
        Storage::fake('public');
        [, $user] = $this->crearSocioConUsuario();

        $archivo = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/me/foto', ['foto' => $archivo]);

        $response->assertStatus(422);
    }

    public function test_rechaza_imagen_demasiado_grande(): void
    {
        Storage::fake('public');
        [, $user] = $this->crearSocioConUsuario();

        $foto = UploadedFile::fake()->image('grande.jpg')->size(4000); // 4MB > 3072KB permitido

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/me/foto', ['foto' => $foto]);

        $response->assertStatus(422);
    }
}
