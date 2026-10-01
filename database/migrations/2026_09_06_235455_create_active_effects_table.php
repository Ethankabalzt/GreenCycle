<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Restricción de efectos duplicados: UNIQUE (tree_id, effect_type, active_flag).
     *
     * active_flag vale 1 mientras el efecto está vigente y NULL cuando expiró.
     * En un índice único los NULL no colisionan entre sí, así que un árbol puede
     * tener muchos efectos ya expirados del mismo tipo, pero como máximo uno
     * vigente. Dos activaciones seguidas del mismo efecto fallan en la base de datos.
     */
    public function up(): void
    {
        Schema::create('active_effects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_item_id')->constrained();
            $table->string('effect_type'); // Copiado de shop_items.effect_type al activar.

            $table->timestamp('activated_at');
            $table->timestamp('expires_at')->nullable();
            $table->tinyInteger('active_flag')->nullable(); // 1 = vigente, NULL = expirado.

            $table->timestamps();

            $table->unique(['tree_id', 'effect_type', 'active_flag'], 'uq_active_effects_tree_type_active');
            $table->index(['active_flag', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_effects');
    }
};
