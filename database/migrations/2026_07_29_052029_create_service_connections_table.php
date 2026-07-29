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
        Schema::create('service_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // 'github', 'google'
            $table->string('external_id'); // id del usuario en GitHub/Google

            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->jsonb('scopes')->nullable();

            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            // indice unico para no duplicar cuentas del mismo proveedor para el mismo usuario
            $table->unique(['user_id', 'provider', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_connections');
    }
};
