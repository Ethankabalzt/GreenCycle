<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Tree;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class TreeCareTest extends TestCase
{
    use RefreshDatabase;

    // 1. Cuidado exitoso
    public function test_cuidado_exitoso(): void
    {
        $user = User::factory()->create();
        $tree = Tree::factory()->for($user)->create();
        Sanctum::actingAs($user);

        $this->postJson("/api/trees/{$tree->id}/care")
            ->assertOk()
            ->assertJsonPath('tree.progress', 1)
            ->assertJsonPath('tree.health', 70);

        $this->assertNotNull($tree->fresh()->next_care_at);
        $this->assertDatabaseCount('cares', 1);
    }

    // 2. Bloqueo por cooldown
    public function test_bloqueo_por_cooldown(): void
    {
        $user = User::factory()->create();
        $tree = Tree::factory()->for($user)->create();
        Sanctum::actingAs($user);

        $this->postJson("/api/trees/{$tree->id}/care")->assertOk();

        $this->postJson("/api/trees/{$tree->id}/care")
            ->assertStatus(409)
            ->assertJsonStructure(['error', 'next_care_at']);

        $this->assertEquals(1, $tree->fresh()->progress);
        $this->assertDatabaseCount('cares', 1);
    }

    // 3. Ascenso de nivel tras 5 cuidados válidos
    public function test_ascenso_de_nivel(): void
    {
        $user = User::factory()->create();
        $tree = Tree::factory()->for($user)->create();
        Sanctum::actingAs($user);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson("/api/trees/{$tree->id}/care")->assertOk();
            $this->travel(61)->minutes(); // salta el cooldown
        }

        $tree->refresh();
        $this->assertEquals(1, $tree->level);
        $this->assertEquals(0, $tree->progress);
    }

    // 4. Evolución a MATURE
    public function test_evolucion_a_mature(): void
    {
        $user = User::factory()->create();
        $tree = Tree::factory()->for($user)->create([
            'level'    => 9,
            'progress' => 4,
        ]);
        Sanctum::actingAs($user);

        $this->postJson("/api/trees/{$tree->id}/care")
            ->assertOk()
            ->assertJsonPath('tree.status', 'MATURE')
            ->assertJsonPath('tree.level', 10);

        $this->assertEquals('MATURE', $tree->fresh()->status);
    }

    // 5. Protección de recursos cruzados
    public function test_proteccion_de_recursos_cruzados(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $tree = Tree::factory()->for($owner)->create();
        Sanctum::actingAs($intruder);

        $this->postJson("/api/trees/{$tree->id}/care")->assertForbidden();

        $this->assertEquals(0, $tree->fresh()->progress);
    }

    // Extra recomendado: estado terminal
    public function test_arbol_muerto_no_recibe_cuidados(): void
    {
        $user = User::factory()->create();
        $tree = Tree::factory()->for($user)->create(['status' => Tree::DEAD]);
        Sanctum::actingAs($user);

        $this->postJson("/api/trees/{$tree->id}/care")->assertStatus(409);
    }
}
