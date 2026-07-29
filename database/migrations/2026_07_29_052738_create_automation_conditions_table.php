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
        Schema::create('automation_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->cascadeOnDelete();
            $table->integer('position'); // para saber el orden de las condiciones
            $table->string('field');
            $table->string('operator');
            $table->jsonb('value')->nullable(); // guardar desde un string hasta un array
            $table->timestamps();
            // indice unico: no pueden haber dos condiciones en la misma posicion para la misma automatizacion
            $table->unique(['automation_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_conditions');
    }
};
