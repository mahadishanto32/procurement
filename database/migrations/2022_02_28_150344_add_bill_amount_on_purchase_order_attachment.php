<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillAmountOnPurchaseOrderAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_attachment', function (Blueprint $table) {
            $table->decimal('bill_amount',10,2)->default(0);
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
            $table->decimal('bill_amount',10,2)->default(0);
        });
    }
}
