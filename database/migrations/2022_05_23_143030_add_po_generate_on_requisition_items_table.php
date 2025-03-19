<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoGenerateOnRequisitionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requisition_items', function (Blueprint $table) {
             $table->enum('po_generate',['no','yes'])->default('no')->after('is_send');
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
            $table->dropColumn('po_generate');
        });
    }
}
