<?php

namespace Tests\Feature;

use App\Models\ActiveEffect;
use App\Models\SeedType;
use App\Models\ShopItem;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReleaseExpiredEffectsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function activateEffect(string $effectType, string $expiresAt): ActiveEffect
    {
        $user = User::factory()->create();
        $seedType = SeedType::create(['name' => 'Roble']);
        $shopItem = ShopItem::create([
            'name' => 'Acelerador',
            'effect_type' => $effectType,
            'cost' => 10,
            'effect_duration_min' => 60,
        ]);

        $tree = Tree::create([
            'user_id' => $user->id,
            'seed_type_id' => $seedType->id,
            'health' => 100,
            'progress' => 0,
            'status' => Tree::ACTIVE,
            'planted_at' => now(),
        ]);

        return ActiveEffect::create([
            'tree_id' => $tree->id,
            'shop_item_id' => $shopItem->id,
            'effect_type' => $shopItem->effect_type,
            'activated_at' => now(),
            'expires_at' => Carbon::parse($expiresAt),
            'active_flag' => ActiveEffect::VIGENT,
        ]);
    }

    public function test_libera_los_efectos_cumplidos_y_permite_activar_de_nuevo(): void
    {
        Carbon::setTestNow('2026-09-30 08:00:00');

        $effect = $this->activateEffect(ShopItem::ACCELERATOR, '2026-09-30 07:00:00');

        Carbon::setTestNow('2026-09-30 08:30:00');
        $this->artisan('effects:release-expired')->assertSuccessful();

        $this->assertNull($effect->fresh()->active_flag);
        $this->assertFalse($effect->fresh()->isVigente());
    }

    public function test_mantiene_vigente_el_efecto_que_aun_no_cumple(): void
    {
        Carbon::setTestNow('2026-09-30 08:00:00');

        $effect = $this->activateEffect(ShopItem::ACCELERATOR, '2026-09-30 09:00:00');

        Carbon::setTestNow('2026-09-30 08:30:00');
        $this->artisan('effects:release-expired')->assertSuccessful();

        $this->assertTrue($effect->fresh()->isVigente());
    }
}
