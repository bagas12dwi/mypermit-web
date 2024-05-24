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
        Schema::table('Permitts', function (Blueprint $table) {
            $table->string('hse_name')->nullable();
            $table->string('manager_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Permitts', function (Blueprint $table) {
            $table->string('hse_name')->nullable();
            $table->string('manager_name')->nullable();
        });
    }
};
