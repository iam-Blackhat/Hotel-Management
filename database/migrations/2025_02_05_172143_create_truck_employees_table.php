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
        Schema::create('truck_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('employee_role')->nullable();
            $table->unsignedBigInteger('employee_phone')->unique();
            $table->string('employee_email')->nullable();
            $table->boolean('employee_status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_employees');
    }
};
