<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_own_data(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/profile', [
            'name' => 'Novo Nome',
            'bio' => 'Desenvolvedor Laravel',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Novo Nome')
            ->assertJsonPath('data.bio', 'Desenvolvedor Laravel');
    }

    public function test_update_profile_validates_name_min_length(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/profile', ['name' => 'ab']);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $this->getJson('/api/user/me')->assertStatus(401);
    }

    public function test_update_profile_so_afeta_o_usuario_autenticado(): void
    {
        $eu = User::factory()->create(['name' => 'Eu Antigo', 'bio' => 'bio antiga']);
        $outro = User::factory()->create(['name' => 'Outro', 'bio' => 'bio do outro']);

        Sanctum::actingAs($eu);

        $this->putJson('/api/user/profile', [
            'name' => 'Eu Novo',
            'bio' => 'bio nova',
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $eu->id, 'name' => 'Eu Novo', 'bio' => 'bio nova']);
        $this->assertDatabaseHas('users', ['id' => $outro->id, 'name' => 'Outro', 'bio' => 'bio do outro']);
    }
}
