<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductAttributes extends Migration
{
    public function up()
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id', false);
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('attribute_option_id', false);
            $table->foreign('attribute_option_id')->references('id')->on('attribute_options')->restrictOnDelete()->cascadeOnUpdate();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_attributes');
    }
}
