<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtisanControllerTest extends TestCase
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

    public function test_desarrollador_puede_ver_la_consola(): void
    {
        $user = $this->crearUsuario('desarrollador');

        $this->actingAs($user)
            ->get(route('artisan.index'))
            ->assertOk()
            ->assertSee('Comandos Artisan');
    }

    public function test_administracion_no_puede_ver_la_consola(): void
    {
        $user = $this->crearUsuario('administracion');

        $this->actingAs($user)
            ->get(route('artisan.index'))
            ->assertForbidden();
    }

    public function test_desarrollador_puede_ejecutar_un_comando_de_la_lista(): void
    {
        $user = $this->crearUsuario('desarrollador');

        $this->actingAs($user)
            ->post(route('artisan.run'), ['comando' => 'cache-clear'])
            ->assertRedirect(route('artisan.index'));

        $this->assertNotNull(session('artisan_output'));
    }

    public function test_no_se_puede_ejecutar_un_comando_fuera_de_la_lista(): void
    {
        $user = $this->crearUsuario('desarrollador');

        $this->actingAs($user)
            ->post(route('artisan.run'), ['comando' => 'migrate:fresh'])
            ->assertNotFound();
    }
}
