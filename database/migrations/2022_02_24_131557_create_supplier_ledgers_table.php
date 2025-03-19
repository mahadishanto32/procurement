<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_payment_id', false)->nullable();
            
            $table->date('date');
            
            $table->decimal('opening_balance',10,2)->default(0);
            $table->decimal('debit',10,2)->default(0);
            $table->decimal('credit',10,2)->default(0);
            $table->decimal('closing_balance',10,2)->default(0);
            
            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->timestamps();

            $table->foreign('supplier_payment_id')->references('id')->on('supplier_payments')->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::dropIfExists('supplier_ledgers');
    }
}
