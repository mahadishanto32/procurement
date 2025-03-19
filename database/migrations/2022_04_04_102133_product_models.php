<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductModels extends Migration
{
    public function up()
    {
        // Schema::create('product_models', function (Blueprint $table) {
        //     $table->id();

        //     $table->unsignedBigInteger('product_id', false);
        //     $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete()->cascadeOnUpdate();

        //     $table->string('model')->nullable();
        //     $table->text('model_name')->nullable();
        //     $table->text('description')->nullable();
        //     $table->decimal('unit_price')->default(0);
        //     $table->decimal('tax')->default(0);
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        //Schema::dropIfExists('product_models');
    }
}
