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
        Schema::create('food_dashboard_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck_id')->nullable();
            $table->unsignedBigInteger('total_sales');
            $table->unsignedInteger('total_orders');
            $table->timestamps();

            $table->foreign('truck_id')->references('id')->on('trucks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_dashboard_metrics');
    }
};
