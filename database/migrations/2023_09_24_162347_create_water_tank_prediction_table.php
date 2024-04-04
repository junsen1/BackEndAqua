<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterTankPredictionTable extends Migration
{
    public function up()
    {
        Schema::create('water_tank_prediction_day_hour', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id');
            $table->string('water_parameter');
            $table->double('value'); // You can use double for floating-point values
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('water_tank_prediction_day_hour');
    }
}

