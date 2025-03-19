<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVatDiscountPercentageOnGoodsReceivedItemsStockIn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_received_items_stock_in', function (Blueprint $table) {
            $table->string('vat_percentage',128)->default(0);
            $table->string('discount_percentage',128)->default(0);
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
             $table->string('vat_percentage',128)->default(0);
            $table->string('discount_percentage',128)->default(0);
        });
    }
}
