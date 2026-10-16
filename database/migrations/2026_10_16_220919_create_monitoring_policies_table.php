<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Each published version is a new row and is never edited afterwards.
     */
    public function up(): void
    {
        Schema::create('monitoring_policies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedInteger('version')->unique();
            $table->string('title');
            $table->text('body');
            $table->foreignUlid('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_policies');
    }
};
