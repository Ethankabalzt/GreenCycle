<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConstraintsTest extends TestCase
{
    use RefreshDatabase;

    private int $userId;

    private int $treeId;

    private int $itemId;

    private int $otherItemId;

    protected function setUp(): void
    {
        parent::setUp();

        $now = now();

        $this->userId = DB::table('users')->insertGetId([
            'name' => 'Test',
            'email' => 'test@greencycle.test',
            'password' => bcrypt('secret'),
            'coins' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $seedId = DB::table('seed_types')->insertGetId([
            'name' => 'Roble',
            'cares_by_level' => 3,
            'harvest_coins' => 50,
        ]);

        $this->treeId = DB::table('trees')->insertGetId([
            'user_id' => $this->userId,
            'seed_type_id' => $seedId,
            'planted_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->itemId = DB::table('shop_items')->insertGetId([
            'name' => 'Acelerador',
            'effect_type' => 'ACCELERATOR',
            'cost' => 10,
            'effect_duration_min' => 60,
        ]);

        $this->otherItemId = DB::table('shop_items')->insertGetId([
            'name' => 'Acelerador premium',
            'effect_type' => 'ACCELERATOR',
            'cost' => 25,
            'effect_duration_min' => 120,
        ]);
    }

    private function insertEffect(int $shopItemId, ?int $activeFlag): void
    {
        DB::table('active_effects')->insert([
            'tree_id' => $this->treeId,
            'shop_item_id' => $shopItemId,
            'effect_type' => 'ACCELERATOR',
            'activated_at' => now(),
            'expires_at' => now()->addHour(),
            'active_flag' => $activeFlag,
        ]);
    }

    public function test_no_permite_dos_efectos_vigentes_iguales_en_el_mismo_arbol(): void
    {
        $this->insertEffect($this->itemId, 1);

        $this->expectException(QueryException::class);

        $this->insertEffect($this->itemId, 1);
    }

    public function test_no_permite_dos_efectos_del_mismo_tipo_vigentes_en_el_mismo_arbol(): void
    {
        $this->insertEffect($this->itemId, 1);

        $this->expectException(QueryException::class);

        $this->insertEffect($this->otherItemId, 1);
    }

    public function test_permite_un_efecto_nuevo_cuando_el_anterior_ya_expiro(): void
    {
        $this->insertEffect($this->itemId, null); // expirado
        $this->insertEffect($this->otherItemId, null); // otro expirado
        $this->insertEffect($this->itemId, 1);    // uno vigente

        $this->assertSame(3, DB::table('active_effects')->count());
        $this->assertSame(1, DB::table('active_effects')->where('active_flag', 1)->count());
    }

    public function test_no_permite_filas_duplicadas_de_inventario_para_el_mismo_item(): void
    {
        DB::table('inventory_items')->insert([
            'user_id' => $this->userId,
            'shop_item_id' => $this->itemId,
            'quantity' => 1,
        ]);
        
        $this->expectException(QueryException::class);

        DB::table('inventory_items')->insert([
            'user_id' => $this->userId,
            'shop_item_id' => $this->itemId,
            'quantity' => 1,
        ]);
    }

    public function test_no_permite_cantidad_negativa_en_inventario(): void
    {
        $this->expectException(QueryException::class);

        DB::table('inventory_items')->insert([
            'user_id' => $this->userId,
            'shop_item_id' => $this->itemId,
            'quantity' => -1,
        ]);
    }

    public function test_trees_tiene_last_decay_at_y_harvested_at(): void
    {
        $tree = DB::table('trees')->where('id', $this->treeId)->first();

        $this->assertTrue(property_exists($tree, 'last_decay_at'));
        $this->assertTrue(property_exists($tree, 'harvested_at'));
        $this->assertNull($tree->last_decay_at);
        $this->assertNull($tree->harvested_at);
    }
}
