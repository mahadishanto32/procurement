<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CostCentres extends Migration
{
    public function up()
    {
        Schema::create('cost_centres', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('company_id', false);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->text('address')->nullable();
            $table->text('logo')->nullable();
            $table->text('banner')->nullable();
            
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
        Schema::dropIfExists('cost_centres');
    }
}
