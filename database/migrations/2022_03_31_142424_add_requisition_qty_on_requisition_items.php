<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequisitionQtyOnRequisitionItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requisition_items', function (Blueprint $table) {
             $table->decimal('requisition_qty',10,2)->default(0)->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requisition_items', function (Blueprint $table) {
            $table->dropColumn('requisition_qty');
        });
    }
}
