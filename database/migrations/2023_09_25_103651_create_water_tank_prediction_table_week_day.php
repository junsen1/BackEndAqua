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
        Schema::create('water_tank_prediction_week_day', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id');
            $table->string('water_parameter');
            $table->double('value'); // You can use double for floating-point values
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_tank_prediction_table_week_day');
    }
};
