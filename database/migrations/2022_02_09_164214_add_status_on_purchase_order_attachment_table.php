<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusOnPurchaseOrderAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_attachment', function (Blueprint $table) {
            $table->enum('status',['pending','approved','halt'])->default('pending');
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
            $table->enum('status',['pending','approved','halt'])->default('pending');
        });
    }
}
