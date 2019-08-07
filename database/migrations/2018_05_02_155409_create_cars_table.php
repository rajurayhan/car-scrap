<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('image_url', 1000);
            $table->string('delay');
            $table->string('price');
            $table->string('color');
            $table->string('upholstery');
            $table->string('combustible');
            $table->string('consumo_mixto');
            $table->string('emisiones_de');
            $table->string('reserved');
            $table->string('options', 1000);
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
        Schema::dropIfExists('cars');
    }
}
