<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsReceivedItemsStockInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_received_items_stock_in', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('goods_received_item_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();

            $table->decimal('unit_amount')->default(0);
            $table->integer('received_qty',false,10)->default(0);
            $table->decimal('sub_total')->default(0);
            $table->string('discount',128)->default(0);
            $table->string('vat',128)->default(0);
            $table->decimal('total_amount')->default(0);

            $table->enum('is_grn_complete',['no','yes'])->nullable();
           
            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();

            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('goods_received_item_id')->references('id')->on('goods_received_items')->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete()->cascadeOnUpdate();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_received_items_stock_in');
    }
}
