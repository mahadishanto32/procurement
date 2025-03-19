<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGrnIdOnPurchaseOrderAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_attachment', function (Blueprint $table) {
            $table->unsignedBigInteger('goods_received_note_id', false)->nullable();
            $table->enum('bill_type',array('po','grn'))->default('po');
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
        Schema::table('purchase_order_attachment', function (Blueprint $table) {
            $table->unsignedBigInteger('goods_received_note_id', false)->nullable();
            $table->enum('bill_type',array('po','grn'))->default('po');
            $table->foreign('goods_received_note_id')->references('id')->on('goods_received_notes')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
}
