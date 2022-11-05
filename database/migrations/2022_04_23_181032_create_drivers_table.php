<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('en_name')->unique();
            $table->string('ar_name')->unique();
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('photo')->nullable();
            $table->float('lon')->nullable();
            $table->float('lat')->nullable();
            $table->string('password')->nullable();
            $table->boolean('isOnline')->default(0);
            $table->boolean('status')->default(0);
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->text('fcm_token')->nullable();

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
        Schema::dropIfExists('drivers');
    }
}
