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
        Schema::create('execution_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_execution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('automation_action_id')->constrained()->cascadeOnDelete();

            $table->integer('position');
            $table->string('status')->default('pending');
            $table->integer('attempts')->default(0); // intentos (Backoff)

            $table->jsonb('input_payload')->nullable();
            $table->jsonb('output_payload')->nullable();
            $table->jsonb('error_details')->nullable();

            $table->timestamps();

            // Un paso especifico para una ejecucion específica
            $table->unique(['automation_execution_id', 'automation_action_id'], 'exec_action_unique');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('execution_steps');
    }
};
