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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('paid_status', ['cash', 'visa','link'])->default('cash')->change();
            $table->bigInteger('restaurant_id')->unsigned()->nullable()->change();
            $table->string('origin_lat')->nullable();
            $table->string('origin_lng')->nullable();
            $table->longText('origin_address')->nullable();

            $table->string('receipt_type')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('origin_lat');
            $table->dropColumn('origin_lng');
            $table->dropColumn('origin_address');
            $table->dropColumn('receipt_type');
        });
    }
}