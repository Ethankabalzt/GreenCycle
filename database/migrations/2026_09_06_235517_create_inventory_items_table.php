<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Restricciones de inventario:
     * - UNIQUE (user_id, shop_item_id): un usuario no puede tener dos filas para el
     *   mismo ítem; las cantidades se acumulan en quantity.
     * - quantity >= 0, implementado según el motor: PostgreSQL usa un CHECK,
     *   MySQL lo aplica con el tipo unsigned y SQLite (soloused en tests) con un
     *   trigger BEFORE INSERT/UPDATE que aborta la sentencia.
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_item_id')->constrained();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'shop_item_id']);
        });

        $this->guardNonNegativeQuantity('inventory_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS inventory_items_quantity_non_negative_insert');
            DB::statement('DROP TRIGGER IF EXISTS inventory_items_quantity_non_negative_update');
        }

        Schema::dropIfExists('inventory_items');
    }

    private function guardNonNegativeQuantity(string $table): void
    {
        $message = 'inventory_items.quantity debe ser mayor o igual a 0';

        match (DB::getDriverName()) {
            'pgsql' => DB::statement(sprintf(
                'ALTER TABLE %s ADD CONSTRAINT inventory_items_quantity_non_negative CHECK (quantity >= 0)',
                $table,
            )),
            'sqlite' => $this->createSqliteGuard($table, $message),
            default => null,
        };
    }

    /**
     * SQLite no admite agregar constraints a una tabla existente, así que la regla
     * se aplica con triggers que abortan la sentencia, igual que haría el CHECK.
     */
    private function createSqliteGuard(string $table, string $message): void
    {
        foreach (['insert', 'update'] as $event) {
            DB::statement(sprintf(
                'CREATE TRIGGER %s_quantity_non_negative_%s BEFORE %s ON %s WHEN NEW.quantity < 0 BEGIN SELECT RAISE(ABORT, %s); END',
                $table,
                $event,
                strtoupper($event),
                $table,
                "'".str_replace("'", "''", $message)."'",
            ));
        }
    }
};
