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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permitt_id')->nullable()->constrained('permitts')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('action');
            $table->dateTime('date')->default(now('Asia/Jakarta')); // Set default value to current date and time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
