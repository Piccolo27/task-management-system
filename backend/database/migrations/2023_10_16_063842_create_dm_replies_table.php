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
        if (!Schema::hasTable('dm_replies')) {
            Schema::create('dm_replies', function (Blueprint $table) {
                $table->id('dm_reply_id');
                $table->unsignedBigInteger('dm_thread_id');
                $table->foreign('dm_thread_id')->references('dm_thread_id')->on('dm_threads')->onDelete('cascade');
                $table->string('body');
                $table->unsignedBigInteger('created_by');
                $table->foreign('created_by')->references('employee_id')->on('employees')->onDelete('cascade');
                $table->unsignedBigInteger('updated_by');
                $table->foreign('updated_by')->references('employee_id')->on('employees')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('dm_replies')) {
            Schema::dropIfExists('dm_replies');
        }
    }
};
