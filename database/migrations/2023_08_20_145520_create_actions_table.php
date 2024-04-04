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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('start_time');
            $table->string('end_time');
            $table->string('color');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
};
