<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnChangeFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_change_faq', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('faq_id', false)->nullable();
            $table->unsignedBigInteger('goods_received_item_id', false)->nullable();
            $table->timestamps();

            $table->foreign('faq_id')->references('id')->on('faq')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('goods_received_item_id')->references('id')->on('goods_received_items')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_change_faq');
    }
}
