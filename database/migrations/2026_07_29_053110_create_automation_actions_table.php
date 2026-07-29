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
        Schema::create('automation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->cascadeOnDelete();
            // puede ser null si es una accion interna que no requiere proveedor externo
            $table->foreignId('service_connection_id')->nullable()->constrained()->nullOnDelete();

            $table->string('type'); // ej: gmail.send, github.create_issue
            $table->integer('position'); // el orden en el que se ejecuta
            $table->jsonb('config')->nullable(); // parametros especificos
            $table->timestamps();

            // indice unico
            $table->unique(['automation_id', 'position']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_actions');
    }
};
