\<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('requisition_item_id');
            $table->text('messages')->nullable();
            $table->enum('type',['unread','read'])->default('unread');
            $table->enum('status', array('requisition','send-to-department-head','sent-to-purchase','send-to-store','send-to-gate-manager','send-to-quality-manager','send-to-billing','send-to-audit','send-to-accounts'))->nullable();
            $table->timestamp('read_at')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
