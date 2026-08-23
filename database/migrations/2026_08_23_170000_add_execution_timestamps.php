<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('automation_executions', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });

        Schema::table('execution_steps', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('attempts');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('execution_steps', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'completed_at']);
        });

        Schema::table('automation_executions', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'completed_at']);
        });
    }
};
