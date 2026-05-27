<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_unauthenticated_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_login_page_renders(): void
    {
        $this->withoutVite()->get(route('login'))->assertOk();
    }

    public function test_admin_can_access_socios_index(): void
    {
        $user = User::factory()->create(['rol' => 'administracion']);

        $this->withoutVite()->actingAs($user)
            ->get(route('socios.index'))
            ->assertOk();
    }

    public function test_socio_role_cannot_access_socios_index(): void
    {
        $user = User::factory()->create(['rol' => 'socio', 'socio_id' => null]);

        $this->actingAs($user)
            ->get(route('socios.index'))
            ->assertForbidden();
    }

    public function test_developer_can_access_usuarios(): void
    {
        $user = User::factory()->create(['rol' => 'desarrollador']);

        $this->withoutVite()->actingAs($user)
            ->get(route('usuarios.index'))
            ->assertOk();
    }

    public function test_admin_cannot_access_usuarios(): void
    {
        $user = User::factory()->create(['rol' => 'administracion']);

        $this->actingAs($user)
            ->get(route('usuarios.index'))
            ->assertForbidden();
    }
}
