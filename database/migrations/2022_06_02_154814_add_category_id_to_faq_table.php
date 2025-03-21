<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id', false)->after('id');

            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->dropForeign([
                'category_id'
            ]);

            $table->dropColumn([
                'category_id'
            ]);
        });
    }
}
