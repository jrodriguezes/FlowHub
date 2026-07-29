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
        Schema::create('automation_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // para la privacidad (RF-10)

            $table->string('event_key')->nullable(); // llave unica del evento que lo disparo
            $table->string('status')->default('pending');

            $table->jsonb('input_payload')->nullable(); // los datos que entraron
            $table->jsonb('output_payload')->nullable(); // lo que resulto al final
            $table->jsonb('error_details')->nullable(); // si fallo todo, el por que

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_executions');
    }
};
