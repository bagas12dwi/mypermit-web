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
        Schema::create('permitts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('permitt_number');
            $table->string('work_category');
            $table->string('project_name');
            $table->string('date');
            $table->string('time');
            $table->string('type_of_work');
            $table->string('kontrol_pengendalian');
            $table->string('organic')->nullable()->default('');
            $table->integer('workers')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable()->default('');
            $table->string('tools_used')->nullable()->default('');
            $table->string('lifting_distance')->nullable()->default('');
            $table->boolean('gas_measurements')->default(false);
            $table->double('oksigen')->nullable();
            $table->double('karbon_dioksida')->nullable();
            $table->double('hidrogen_sulfida')->nullable();
            $table->double('lel')->nullable();
            $table->boolean('aman_masuk')->default(false);
            $table->boolean('aman_hotwork')->default(false);
            $table->boolean('is_approve_hse')->default(false);
            $table->boolean('is_approve_manager')->default(false);
            $table->string('status')->default('Menunggu');
            $table->string('status_permit')->default('Open');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permitts');
    }
};
