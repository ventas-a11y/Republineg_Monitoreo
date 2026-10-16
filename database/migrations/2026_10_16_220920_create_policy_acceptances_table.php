<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Acceptance records are evidence of consent (LOPDP): they are only ever
     * inserted. On PostgreSQL the application role also loses UPDATE and
     * DELETE on the table, so immutability does not depend on the code alone.
     */
    public function up(): void
    {
        Schema::create('policy_acceptances', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('monitoring_policy_id')->constrained()->restrictOnDelete();
            $table->timestamp('accepted_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();

            $table->unique(['user_id', 'monitoring_policy_id']);
            $table->index('monitoring_policy_id');
        });

        $appRole = config('database.connections.pgsql.username');

        if (DB::getDriverName() === 'pgsql' && $appRole !== null && $appRole !== DB::connection()->getConfig('username')) {
            DB::statement('REVOKE UPDATE, DELETE ON policy_acceptances FROM '.DB::getQueryGrammar()->wrap($appRole));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_acceptances');
    }
};
