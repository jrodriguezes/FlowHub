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
        Schema::create('dead_letter_messages', function (Blueprint $table) {
            $table->id();
            $table->string('queue');
            $table->jsonb('payload'); // los datos originales del trabajo que fallo
            $table->text('exception'); // el error gigante que provoco el fallo definitivo
            $table->timestamp('failed_at')->useCurrent();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dead_letter_messages');
    }
};
