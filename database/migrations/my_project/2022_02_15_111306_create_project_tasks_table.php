<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sub_deliverable_id');
            $table->string('name');
            $table->enum('status', ['pending', 'processing', 'done', 'approved', 'halt'])->default('pending');
            $table->double('hour');
            $table->dateTime('initiate_time_line');
            $table->dateTime('end_time_line');
            $table->longText('remarks')->nullable();
            $table->integer('weightage');
            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists('project_tasks');
    }
}
