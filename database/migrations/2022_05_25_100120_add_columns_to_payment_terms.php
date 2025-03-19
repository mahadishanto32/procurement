<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPaymentTerms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_terms', function (Blueprint $table) {
            $table->integer('days')->after('percentage')->default(0);
            $table->enum('type', array('paid','due'))->default('paid')->after('days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_terms', function (Blueprint $table) {
            $table->dropColumn([
                'days',
                'type',
            ]);
        });
    }
}
