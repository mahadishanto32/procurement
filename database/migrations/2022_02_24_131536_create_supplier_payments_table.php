<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id', false)->nullable();
            $table->unsignedBigInteger('purchase_order_id', false)->nullable();

            $table->dateTime('transection_date')->nullable();
            $table->enum('transection_type',array('purchase','payment'))->nullable();
            $table->decimal('bill_amount',10,2)->default(0);
            $table->decimal('pay_amount',10,2)->default(0);
            $table->dateTime('pay_date')->nullable();

            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::dropIfExists('supplier_payments');
    }
}
