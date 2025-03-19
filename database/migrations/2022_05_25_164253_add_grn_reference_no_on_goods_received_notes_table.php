<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGrnReferenceNoOnGoodsReceivedNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->string('grn_reference_no',32)->after('reference_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->dropColumn('grn_reference_no');
        });
    }
}
