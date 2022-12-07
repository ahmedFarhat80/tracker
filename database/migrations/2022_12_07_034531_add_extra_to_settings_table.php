<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->double('amount_start_delivery')->nullable()->default(null);
            $table->double('maximum_distance')->nullable()->default(null);
            $table->double('start_number_km')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('amount_start_delivery');
            $table->dropColumn('maximum_distance');
            $table->dropColumn('start_number_km');
        });
    }
}
