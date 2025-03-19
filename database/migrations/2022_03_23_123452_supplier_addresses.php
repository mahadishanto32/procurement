<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SupplierAddresses extends Migration
{
    public function up()
    {
        Schema::create('supplier_addresses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('supplier_id', false);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();

            $table->enum('type', array('corporate','factory'))->default('corporate')->nullable();

            $table->string('road')->nullable();
            $table->string('village')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('zip')->nullable();
            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_addresses');
    }
}
