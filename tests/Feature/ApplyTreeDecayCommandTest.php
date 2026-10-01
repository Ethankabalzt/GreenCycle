<?php

namespace Tests\Feature;

use App\Models\SeedType;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ApplyTreeDecayCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function plantTreeAt(string $plantedAt, int $health = 100, string $status = Tree::ACTIVE): Tree
    {
        $user = User::factory()->create();
        $seedType = SeedType::create(['name' => 'Roble']);

        return Tree::create([
            'user_id' => $user->id,
            'seed_type_id' => $seedType->id,
            'level' => 0,
            'health' => $health,
            'progress' => 0,
            'status' => $status,
            'planted_at' => Carbon::parse($plantedAt),
            'next_decay_at' => Carbon::parse($plantedAt)->addHours(Tree::DECAY_INTERVAL_HOURS),
        ]);
    }

    public function test_el_deterioro_no_se_repite_dentro_del_mismo_intervalo(): void
    {
        $tree = $this->plantTreeAt('2026-09-30 02:00:00');

        Carbon::setTestNow('2026-09-30 08:00:00');
        $this->artisan('trees:apply-decay')->assertSuccessful();

        $this->assertSame(80, (int) $tree->fresh()->health);
        $this->assertSame('2026-09-30 08:00:00', $tree->fresh()->last_decay_at->toDateTimeString());
        $this->assertSame('2026-09-30 14:00:00', $tree->fresh()->next_decay_at->toDateTimeString());

        Carbon::setTestNow('2026-09-30 08:10:00');
        $this->artisan('trees:apply-decay')->assertSuccessful();

        $this->assertSame(80, (int) $tree->fresh()->health);
        $this->assertSame('2026-09-30 08:00:00', $tree->fresh()->last_decay_at->toDateTimeString());
    }

    public function test_el_deterioro_se_acumula_por_cada_intervalo_vencido(): void
    {
        $tree = $this->plantTreeAt('2026-09-30 02:00:00');

        Carbon::setTestNow('2026-09-30 14:00:00');
        $this->artisan('trees:apply-decay')->assertSuccessful();

        $this->assertSame(60, (int) $tree->fresh()->health);
        $this->assertSame('2026-09-30 14:00:00', $tree->fresh()->last_decay_at->toDateTimeString());
    }

    public function test_el_arbol_muere_cuando_la_salud_llega_a_cero(): void
    {
        $tree = $this->plantTreeAt('2026-09-30 02:00:00', health: 20);

        Carbon::setTestNow('2026-09-30 08:00:00');
        $this->artisan('trees:apply-decay')->assertSuccessful();

        $this->assertSame(0, (int) $tree->fresh()->health);
        $this->assertSame(Tree::DEAD, $tree->fresh()->status);
    }

    public function test_no_procesa_arboles_que_no_estan_activos(): void
    {
        $tree = $this->plantTreeAt('2026-09-30 02:00:00', status: Tree::HARVESTED);

        Carbon::setTestNow('2026-09-30 14:00:00');
        $this->artisan('trees:apply-decay')->assertSuccessful();

        $this->assertSame(100, (int) $tree->fresh()->health);
        $this->assertNull($tree->fresh()->last_decay_at);
    }

    public function test_el_arbol_sin_checkpoint_usa_la_fecha_de_siembra(): void
    {
        $tree = $this->plantTreeAt('2026-09-30 02:00:00');
        $tree->forceFill(['last_decay_at' => null, 'next_decay_at' => null])->save();

        Carbon::setTestNow('2026-09-30 08:00:00');
        $this->artisan('trees:apply-decay')->assertSuccessful();

        $this->assertSame(80, (int) $tree->fresh()->health);
        $this->assertSame('2026-09-30 08:00:00', $tree->fresh()->last_decay_at->toDateTimeString());
    }
}
