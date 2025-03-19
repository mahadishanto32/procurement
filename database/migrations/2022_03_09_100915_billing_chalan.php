<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BillingChalan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('billing_chalan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_attachment_id', false)->nullable();
            $table->string('challan_no',64);
              
            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->timestamps();

            $table->foreign('purchase_order_attachment_id')->references('id')->on('purchase_order_attachment')->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_chalan');
    }
}
