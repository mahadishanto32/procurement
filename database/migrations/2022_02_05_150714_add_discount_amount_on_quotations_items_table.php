<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountAmountOnQuotationsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations_items', function (Blueprint $table) {
            $table->string('discount_amount',128)->default(0);
            $table->string('vat_percentage',128)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations_items', function (Blueprint $table) {
            $table->string('discount_amount',128)->default(0);
            $table->string('vat_percentage',128)->default(0);
        });
    }
}
