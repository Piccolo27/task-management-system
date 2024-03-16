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
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id('task_id');
                $table->unsignedBigInteger('project_id');
                $table->foreign('project_id')->references('project_id')->on('projects');
                $table->string('title');
                $table->string('description');
                $table->unsignedBigInteger('assigned_member_id')->nullable()->default(0);
                $table->unsignedBigInteger('estimate_hr');
                $table->unsignedBigInteger('actual_hr')->nullable();
                $table->integer('status')->default(0);
                $table->dateTime('estimate_start_date')->nullable();
                $table->dateTime('estimate_finish_date')->nullable();
                $table->dateTime('actual_start_date')->nullable();
                $table->dateTime('actual_finish_date')->nullable();
                $table->timestamps();
                $table->dateTime('deleted_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tasks')) {
            Schema::dropIfExists('tasks');
        }
    }
};
