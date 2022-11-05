<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->bigInteger('restaurant_id')->unsigned();
            $table->string('order_no')->unique();
            $table->string('client_name')->nullable();
            $table->string('mobile')->nullable();
            $table->longText('address')->nullable();
            $table->decimal('price')->nullable();
            $table->longText('details')->nullable();
            $table->string('lon')->nullable();
            $table->string('lat')->nullable();
            $table->enum('status' , ['pending' , 'approved' , 'delivered'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
