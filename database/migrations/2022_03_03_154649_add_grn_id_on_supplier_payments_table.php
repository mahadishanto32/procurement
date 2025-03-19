<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGrnIdOnSupplierPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
             $table->unsignedBigInteger('goods_received_note_id', false)->nullable();
             $table->enum('bill_type',array('po','grn','po-advance'))->default('po');
             $table->enum('status',array('pending','approved','canceled','audited'))->default('pending');
             $table->foreign('goods_received_note_id')->references('id')->on('goods_received_notes')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
             $table->unsignedBigInteger('goods_received_note_id', false)->nullable();
             $table->enum('bill_type',array('po','grn'))->nullable();
              $table->foreign('goods_received_note_id')->references('id')->on('goods_received_notes')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
}
