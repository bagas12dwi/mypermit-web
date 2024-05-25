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
        Schema::table('permitts', function (Blueprint $table) {
            $table->boolean('work_done')->default(false);
            $table->boolean('need_permit')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permitts', function (Blueprint $table) {
            $table->boolean('work_done');
            $table->boolean('need_permit');
        });
    }
};
