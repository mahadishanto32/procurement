<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductModelAttributes extends Migration
{
    public function up()
    {
        // Schema::create('product_model_attributes', function (Blueprint $table) {
        //     $table->id();

        //     $table->unsignedBigInteger('product_model_id', false);
        //     $table->foreign('product_model_id')->references('id')->on('product_models')->restrictOnDelete()->cascadeOnUpdate();

        //     $table->unsignedBigInteger('attribute_option_id', false);
        //     $table->foreign('attribute_option_id')->references('id')->on('attribute_options')->restrictOnDelete()->cascadeOnUpdate();
            
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        //Schema::dropIfExists('product_model_attributes');
    }
}
