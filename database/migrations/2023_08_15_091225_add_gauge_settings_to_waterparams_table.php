<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGaugeSettingsToWaterparamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('water_params', function (Blueprint $table) {
            $table->float('min_level')->default(0.0);
            $table->float('max_level')->default(12.0);
            $table->float('min_safe')->default(4.0); // Add the new field_id column
            $table->float('max_safe')->default(8.0); // Add the new field_id column
            $table->string('normal_color'); // Add the new field_id column
            $table->string('warning_color'); // Add the new field_id column
            $table->string('unit')->default('unit'); // Add the new field_id column
            $table->string('line_graph_webview_link') ;// Add the new field_id column
            $table->string('gauge_webview_link') ;// Add the new field_id column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_params', function (Blueprint $table) {
            $table->dropColumn('min_level');
            $table->dropColumn('max_level');
            $table->dropColumn('min_safe'); // Add the new field_id column
            $table->dropColumn('max_safe'); // Add the new field_id column
            $table->dropColumn('normal_color');
            $table->dropColumn('warning_color'); // Add the new field_id column
            $table->dropColumn('unit'); // Add the new field_id column
            $table->dropColumn('line_graph_webview_link') ;// Add the new field_id column
            $table->dropColumn('gauge_webview_link') ;// Add the new field_id column
        });
    }


}
;