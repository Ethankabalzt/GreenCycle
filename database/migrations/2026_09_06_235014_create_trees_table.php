<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seed_type_id')->constrained();

            $table->unsignedTinyInteger('level')->default(0);
            $table->unsignedTinyInteger('health')->default(100);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('status')->default('ACTIVE'); // ACTIVE, MATURE, HARVESTED, DEAD

            $table->timestamp('planted_at')->useCurrent();
            $table->timestamp('last_cared_at')->nullable();
            $table->timestamp('next_care_at')->nullable();
            $table->timestamp('harvested_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
