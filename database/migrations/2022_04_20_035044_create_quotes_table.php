<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('en_title')->unique();
            $table->string('ar_title')->unique();
            $table->text('en_desc')->nullable();
            $table->text('ar_desc')->nullable();
            $table->integer('drivers_count')->nullable();
            $table->float('cost');
            $table->integer('months'); // MONTHS
            $table->integer('sequence')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('quotes');
    }
}
