<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHolidaiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holidaies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('full_date')->nullable();
            $table->integer('date');
            $table->integer('month');
            $table->integer('year')->nullable();
            $table->boolean('special_holiday')->default(false);
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
        Schema::dropIfExists('holidaies');
    }
}
