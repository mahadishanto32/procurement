<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdOnRequisitionDeliveryItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requisition_delivery_items', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id', false)->nullable();

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
        Schema::table('requisition_delivery_items', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id', false)->nullable();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete()->cascadeOnUpdate();

        });
    }
}
