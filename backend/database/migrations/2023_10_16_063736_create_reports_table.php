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
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id('report_id');
                $table->string('description');
                $table->unsignedBigInteger('report_to');
                $table->foreign('report_to')->references('employee_id')->on('employees');
                $table->unsignedBigInteger('reported_by');
                $table->foreign('reported_by')->references('employee_id')->on('employees');
                $table->date('date');
                $table->unsignedBigInteger('created_by');
                $table->foreign('created_by')->references('employee_id')->on('employees');
                $table->unsignedBigInteger('updated_by');
                $table->foreign('updated_by')->references('employee_id')->on('employees');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reports')) {
            Schema::dropIfExists('reports');
        }
    }
};
