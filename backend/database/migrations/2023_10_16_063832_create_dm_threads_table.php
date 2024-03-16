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
        if (!Schema::hasTable('dm_threads')) {
            Schema::create('dm_threads', function (Blueprint $table) {
                $table->id('dm_thread_id');
                $table->unsignedBigInteger('direct_message_id');
                $table->foreign('direct_message_id')->references('direct_message_id')->on('direct_messages')->onDelete('cascade');
                $table->boolean('owner_unread')->default(1);
                $table->boolean('user_unread')->default(1);
                $table->boolean('dm_updated')->default(0);
                $table->unsignedBigInteger('created_by');
                $table->foreign('created_by')->references('employee_id')->on('employees')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('dm_threads')) {
            Schema::dropIfExists('dm_threads');
        }
    }
};
