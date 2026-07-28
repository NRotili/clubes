<?php

namespace Tests\Feature;

use App\Models\Socio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SocioImportTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(string $rol): User
    {
        return User::create([
            'name' => 'Test '.$rol,
            'email' => $rol.'@test.com',
            'password' => bcrypt('password'),
            'rol' => $rol,
        ]);
    }

    private function mapeoCompleto(): array
    {
        return [
            'apellido' => 'apellido',
            'nombre' => 'nombre',
            'dni' => 'numero_documento',
            'fecha_nacimiento' => 'fecha_nacimiento',
            'genero' => 'genero',
            'categoria' => 'categoria',
            'estado' => 'estado',
        ];
    }

    public function test_administracion_puede_ver_el_formulario_de_importacion(): void
    {
        $user = $this->crearUsuario('administracion');

        $this->actingAs($user)
            ->get(route('socios.importar'))
            ->assertOk()
            ->assertSee('Importar Socios');
    }

    public function test_socio_no_puede_ver_el_formulario_de_importacion(): void
    {
        $user = $this->crearUsuario('socio');

        $this->actingAs($user)
            ->get(route('socios.importar'))
            ->assertForbidden();
    }

    public function test_preview_detecta_columnas_y_sugiere_mapeo(): void
    {
        $user = $this->crearUsuario('administracion');

        $csv = "Apellido,Nombre,DNI,Fecha Nacimiento,Genero,Categoria,Estado\n"
             ."Gomez,Ana,30111222,15/03/1990,F,adulto,activo\n";

        $archivo = UploadedFile::fake()->createWithContent('socios.csv', $csv);

        $response = $this->actingAs($user)
            ->post(route('socios.importar.preview'), ['archivo' => $archivo]);

        $response->assertOk();
        $response->assertViewHas('mapping', function (array $mapping) {
            return $mapping['dni'] === 'numero_documento'
                && $mapping['apellido'] === 'apellido'
                && $mapping['genero'] === 'genero'
                && $mapping['estado'] === 'estado';
        });
    }

    public function test_store_exige_genero_y_estado_mapeados(): void
    {
        // No pasa por preview() a propósito: layouts/app.blade.php declara una
        // función global sin guardar (ver nota en CLAUDE.md) y dos renders del
        // layout en el mismo proceso de PHPUnit lo hacen fatal. Precargamos el
        // archivo temporal directamente para que store() sea el único render.
        $user = $this->crearUsuario('administracion');

        $csv = "Apellido,Nombre,DNI,Fecha Nacimiento,Categoria\n"
             ."Gomez,Ana,30111222,15/03/1990,adulto\n";

        $path = 'imports/socios/test-sin-genero-estado.csv';
        Storage::disk('local')->put($path, $csv);

        $mapping = [
            'apellido' => 'apellido',
            'nombre' => 'nombre',
            'dni' => 'numero_documento',
            'fecha_nacimiento' => 'fecha_nacimiento',
            'categoria' => 'categoria',
        ];

        $store = $this->actingAs($user)->post(route('socios.importar.store'), [
            'archivo' => $path,
            'mapping' => $mapping,
        ]);

        $store->assertOk();
        $store->assertSee('Faltan asignar columnas obligatorias', false);
        $store->assertSee('Género', false);
        $store->assertSee('Estado', false);
        $this->assertSame(0, Socio::count());

        Storage::disk('local')->delete($path);
    }

    public function test_importa_crea_y_actualiza_socios_por_numero_documento(): void
    {
        $user = $this->crearUsuario('administracion');

        $existente = Socio::create([
            'numero_socio' => Socio::generarNumeroSocio(),
            'qr_uuid' => Socio::generarQrUuid(),
            'apellido' => 'Apellido Viejo',
            'nombre' => 'Nombre Viejo',
            'tipo_documento' => 'DNI',
            'numero_documento' => '40555666',
            'fecha_nacimiento' => '1985-05-20',
            'genero' => 'M',
            'categoria' => 'adulto',
            'estado' => 'activo',
            'fecha_alta' => now(),
        ]);

        $csv = "Apellido,Nombre,DNI,Fecha Nacimiento,Genero,Categoria,Estado\n"
             ."Gomez,Ana,30111222,15/03/1990,F,adulto,activo\n"
             ."Perez,Juan,40555666,20/05/1985,M,junior,suspendido\n"
             .",,,,,,\n"; // fila vacía, debe ignorarse

        $archivo = UploadedFile::fake()->createWithContent('socios.csv', $csv);

        $preview = $this->actingAs($user)
            ->post(route('socios.importar.preview'), ['archivo' => $archivo]);

        $preview->assertOk();
        $path = $preview->viewData('archivo');

        $store = $this->actingAs($user)->post(route('socios.importar.store'), [
            'archivo' => $path,
            'mapping' => $this->mapeoCompleto(),
        ]);

        $store->assertRedirect(route('socios.index'));

        $this->assertDatabaseHas('socios', [
            'numero_documento' => '30111222',
            'apellido' => 'Gomez',
            'nombre' => 'Ana',
            'categoria' => 'adulto',
            'genero' => 'F',
            'estado' => 'activo',
        ]);

        $existente->refresh();
        $this->assertSame('Perez', $existente->apellido);
        $this->assertSame('Juan', $existente->nombre);
        $this->assertSame('junior', $existente->categoria);
        $this->assertSame('suspendido', $existente->estado);
        $this->assertSame(2, Socio::count());
    }

    public function test_fila_sin_datos_obligatorios_se_omite_y_se_reporta(): void
    {
        $user = $this->crearUsuario('administracion');

        $csv = "Apellido,Nombre,DNI,Fecha Nacimiento,Genero,Categoria,Estado\n"
             ."SinDocumento,Test,,15/03/1990,F,adulto,activo\n";

        $archivo = UploadedFile::fake()->createWithContent('socios.csv', $csv);

        $preview = $this->actingAs($user)
            ->post(route('socios.importar.preview'), ['archivo' => $archivo]);
        $path = $preview->viewData('archivo');

        $store = $this->actingAs($user)->post(route('socios.importar.store'), [
            'archivo' => $path,
            'mapping' => $this->mapeoCompleto(),
        ]);

        $store->assertRedirect(route('socios.index'));
        $this->assertSame(0, Socio::count());
        $this->assertStringContainsString('omitidos', session('success'));
    }

    public function test_fila_con_genero_o_estado_invalido_se_omite(): void
    {
        $user = $this->crearUsuario('administracion');

        $csv = "Apellido,Nombre,DNI,Fecha Nacimiento,Genero,Categoria,Estado\n"
             ."Gomez,Ana,30111222,15/03/1990,Desconocido,adulto,activo\n"
             ."Perez,Juan,40555666,20/05/1985,M,junior,Enviado\n";

        $archivo = UploadedFile::fake()->createWithContent('socios.csv', $csv);

        $preview = $this->actingAs($user)
            ->post(route('socios.importar.preview'), ['archivo' => $archivo]);
        $path = $preview->viewData('archivo');

        $store = $this->actingAs($user)->post(route('socios.importar.store'), [
            'archivo' => $path,
            'mapping' => $this->mapeoCompleto(),
        ]);

        $store->assertRedirect(route('socios.index'));
        $this->assertSame(0, Socio::count());
        $this->assertStringContainsString('omitidos', session('success'));
    }
}
