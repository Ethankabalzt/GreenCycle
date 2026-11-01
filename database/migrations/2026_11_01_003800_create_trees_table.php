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
            $table->foreignId('seed_type_id')->constrained('seed_types');
            $table->unsignedInteger('level')->default(0);
            $table->unsignedInteger('health')->default(50);
            $table->unsignedInteger('progress')->default(0);
            $table->string('status', 20)->default('ACTIVE'); // ACTIVE | DEAD | MATURE
            $table->timestamp('planted_at')->useCurrent();
            $table->timestamp('last_cared_at')->nullable();
            $table->timestamp('last_decay_at')->nullable();
            $table->timestamp('next_care_at')->nullable();
            $table->timestamp('harvested_at')->nullable();
            $table->timestamps();
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
