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
        Schema::create('water_params', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id');
            $table->string('water_parameter');
            $table->string('chart_id');
            $table->string('chart_title');
            $table->boolean('show_graph')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_params');
    }
};
