<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierPaymentTermsIdOnQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_payment_terms_id', false)->nullable();

            $table->foreign('supplier_payment_terms_id')->references('id')->on('supplier_payment_terms')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_payment_terms_id', false)->nullable();

            $table->foreign('supplier_payment_terms_id')->references('id')->on('supplier_payment_terms')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
}
