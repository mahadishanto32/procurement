<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostCentreIdToEntryItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entry_items', function (Blueprint $table) {
            $table->unsignedBigInteger('cost_centre_id')->after('id');
            $table->foreign('cost_centre_id')->references('id')->on('cost_centres')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry_items', function (Blueprint $table) {
            $table->dropForeign(['cost_centre_id']);
            $table->dropColumn('cost_centre_id');
        });
    }
}
