<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIdToWaterparamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('water_params', function (Blueprint $table) {
            $table->string('field_id'); // Add the new field_id column

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_params', function (Blueprint $table) {
            $table->dropColumn('field_id');
        });
    }


}
;