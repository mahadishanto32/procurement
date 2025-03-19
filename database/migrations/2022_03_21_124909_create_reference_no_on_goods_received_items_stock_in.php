<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferenceNoOnGoodsReceivedItemsStockIn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_received_items_stock_in', function (Blueprint $table) {
             $table->string('reference_no',128)->after('warehouse_id')->nullable();
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
            $table->dropColumn('reference_no');
        });
    }
}
