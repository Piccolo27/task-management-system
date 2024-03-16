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
        if (!Schema::hasTable('employee_notification')) {
            Schema::create('employee_notification', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
                $table->unsignedBigInteger('notification_id');
                $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
                $table->boolean('is_visible')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employee_notification')) {
            Schema::dropIfExists('employee_notification');
        }
    }
};
