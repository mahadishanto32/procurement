<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EntryTypes extends Migration
{
    public function up()
    {
        Schema::create('entry_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->integer('restriction')->default(1);

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
        Schema::dropIfExists('entry_types');
    }
}
