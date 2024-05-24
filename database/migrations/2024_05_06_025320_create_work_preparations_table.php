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
        Schema::create('work_preparations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permitt_id')->nullable()->constrained('permitts')->onDelete('cascade')->onUpdate('cascade');
            $table->string('pertanyaan');
            $table->boolean('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_preparations');
    }
};
