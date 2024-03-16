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
        if (!Schema::hasTable('thread_employee')) {
            Schema::create('thread_employee', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dm_thread_id');
                $table->foreign('dm_thread_id')->references('dm_thread_id')->on('dm_threads')->onDelete('cascade');
                $table->unsignedBigInteger('employee_id');
                $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
                $table->boolean('user_unread')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('thread_employee')) {
            Schema::dropIfExists('thread_employee');
        }
    }
};
