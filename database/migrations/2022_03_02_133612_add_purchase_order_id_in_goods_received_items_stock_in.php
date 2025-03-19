<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseOrderIdInGoodsReceivedItemsStockIn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_received_items_stock_in', function (Blueprint $table) {

              $table->unsignedBigInteger('purchase_order_id', false)->nullable();
              $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_received_items_stock_in', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
        });
    }
}
