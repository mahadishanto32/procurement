<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Entries extends Migration
{
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            
            $table->unsignedBigInteger('fiscal_year_id', false);
            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->restrictOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('entry_type_id', false);
            $table->foreign('entry_type_id')->references('id')->on('entry_types')->restrictOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('tag_id', false);
            $table->foreign('tag_id')->references('id')->on('tags')->restrictOnDelete()->cascadeOnUpdate();
            
            $table->string('number')->nullable();
            $table->date('date');
            $table->decimal('debit')->default(0);
            $table->decimal('credit')->default(0);
            $table->text('notes')->nullable();
            
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
        Schema::dropIfExists('entries');
    }
}
