<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubDeliverablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_deliverables', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deliverable_id');
            $table->string('name');
            $table->integer('weightage');
            $table->string('start_at');
            $table->string('end_at');
            $table->double('status_at')->default(0.0)->comment('This field will show at percentage value');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
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
        Schema::dropIfExists('sub_deliverables');
    }
}
