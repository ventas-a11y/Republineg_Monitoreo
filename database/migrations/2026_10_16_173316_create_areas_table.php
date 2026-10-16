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
        Schema::create('areas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // An Empleado belongs to exactly one Área.
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUlid('area_id')->nullable()->constrained()->nullOnDelete();
        });

        // A Revisor reviews one or more Áreas.
        Schema::create('area_reviewer', function (Blueprint $table) {
            $table->foreignUlid('area_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['area_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_reviewer');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
        });

        Schema::dropIfExists('areas');
    }
};
