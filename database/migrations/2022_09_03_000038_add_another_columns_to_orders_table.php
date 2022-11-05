<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnotherColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('flat')->nullable();
            $table->string('floor')->nullable();
            $table->string('building')->nullable();
            $table->enum('flat_type' , ['office' , 'flat' , 'house'])->nullable();
            
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
            $table->dropColumn('street');
            $table->dropColumn('flat');
            $table->dropColumn('floor');
            $table->dropColumn('building');
            $table->dropColumn('flat_type');
        });
    }
}
