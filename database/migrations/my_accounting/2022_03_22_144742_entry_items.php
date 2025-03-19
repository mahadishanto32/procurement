<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntryItems extends Migration
{
    public function up()
    {
        Schema::create('entry_items', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('entry_id', false);
            $table->foreign('entry_id')->references('id')->on('entries')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('chart_of_account_id', false);
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->cascadeOnDelete()->cascadeOnUpdate();
            
            $table->decimal('amount')->default(0);
            $table->enum('debit_credit', array('D', 'C'))->default('C')->nullable();
            $table->date('reconciliation_date')->nullable();
            $table->text('narration')->nullable();
            
            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entry_items');
    }
}
