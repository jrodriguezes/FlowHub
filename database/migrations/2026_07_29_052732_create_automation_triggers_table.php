<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('automation_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // ej: webhook o schedule
            $table->string('provider')->nullable(); // ej: github

            $table->jsonb('config')->nullable(); // configuraciones extras

            // Campos para cron/programados
            $table->string('cron_expression')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamp('next_run_at')->nullable();

            // Campo para webhooks
            $table->text('webhook_secret')->nullable(); // guardado como TEXT por si se cifra
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_triggers');
    }
};
