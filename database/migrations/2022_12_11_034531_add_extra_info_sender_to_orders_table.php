<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraInfoSenderToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('from_client_name')->nullable();
            $table->string('from_mobile')->nullable();
            $table->string('from_flat')->nullable();
            $table->string('from_building')->nullable();
            $table->string('from_floor')->nullable();
            $table->string('from_street')->nullable();
            $table->string('from_flat_type')->nullable();
            $table->string('from_piece')->nullable();
            $table->string('piece')->nullable();
            $table->string('from_avenue')->nullable();
            $table->string('avenue')->nullable();
            $table->string('from_details')->nullable();


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
