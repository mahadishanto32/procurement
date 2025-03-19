<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SupplierContactPersons extends Migration
{
    public function up()
    {
        Schema::create('supplier_contact_persons', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('supplier_id', false);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();

            $table->enum('type', array('general', 'sales', 'after-sales'))->default('sales')->nullable();
            
            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_contact_persons');
    }
}
