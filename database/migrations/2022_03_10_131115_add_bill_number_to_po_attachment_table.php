<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillNumberToPoAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_attachment', function (Blueprint $table) {
            $table->string('bill_number',128)->after('bill_type')->nullable();
            $table->text('remarks')->after('bill_number')->nullable();

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
             $table->dropColumn('bill_number');
             $table->dropColumn('remarks');
        });
    }
}
