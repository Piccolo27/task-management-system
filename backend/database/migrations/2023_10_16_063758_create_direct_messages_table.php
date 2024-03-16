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
        if (!Schema::hasTable('direct_messages')) {
            Schema::create('direct_messages', function (Blueprint $table) {
                $table->id('direct_message_id');
                $table->unsignedBigInteger('owner_id');
                $table->foreign('owner_id')->references('employee_id')->on('employees')->onDelete('cascade');
                $table->string('title');
                $table->string('body')->nullable();
                $table->integer('replyable');
                $table->dateTime('start_at');
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
        if (Schema::hasTable('direct_messages')) {
            Schema::dropIfExists('direct_messages');
        }
    }
};
