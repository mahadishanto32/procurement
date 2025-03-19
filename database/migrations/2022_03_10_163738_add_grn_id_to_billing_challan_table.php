<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGrnIdToBillingChallanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_chalan', function (Blueprint $table) {
            $table->dropColumn('challan_no');
            $table->unsignedBigInteger('goods_received_note_id', false)->after('purchase_order_attachment_id')->nullable();

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
        Schema::table('billing_chalan', function (Blueprint $table) {
            $table->string('challan_no',64);
            $table->dropColumn('goods_received_note_id');
        });
    }
}
