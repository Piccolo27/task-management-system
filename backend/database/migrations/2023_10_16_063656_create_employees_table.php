<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id('employee_id');
                $table->string('employee_name');
                $table->string('email')->unique();
                $table->string('password')->nullable()->default(Hash::make(config('app.employee_default_password')));
                $table->string('profile');
                $table->string('position', 1)->default('1');
                $table->string('address')->nullable();
                $table->date('dob')->nullable();
                $table->string('phone')->nullable();
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
        if (Schema::hasTable('employees')) {
            Schema::dropIfExists('employees');
        }
    }
};
