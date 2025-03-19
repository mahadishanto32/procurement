<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBufferInventoryToInventorySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_summaries', function (Blueprint $table) {
            $table->integer('buffer_inventory')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_summaries', function (Blueprint $table) {
            $table->integer('buffer_inventory')->default(0);
        });
    }
}
