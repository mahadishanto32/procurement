<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AttributeOptions extends Migration
{
    public function up()
    {
        Schema::create('attribute_options', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('attribute_id', false);
            $table->foreign('attribute_id')->references('id')->on('attributes')->restrictOnDelete()->cascadeOnUpdate();

            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attribute_options');
    }
}
