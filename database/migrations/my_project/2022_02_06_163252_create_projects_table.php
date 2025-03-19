<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('indent_no')->unique();
            $table->string('name');
            $table->string('work_location');
            $table->string('work_reason');
            $table->longText('details');
            $table->longText('sponsors')->nullable();
            $table->longText('terms')->nullable();
            $table->longText('risk')->nullable();
            $table->string('start_date');
            $table->string('end_date');
            $table->string('items_dimension')->nullable();
            $table->enum('type', ['treading', 'manufacture']);
            $table->enum('status',['pending', 'approved', 'halt'])->default('pending');
            $table->double('status_at')->default(0.0)->comment('This field will show at percentage value');
            $table->bigInteger('approved_by')->nullable();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->softDeletes();
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
        Schema::dropIfExists('projects');
    }
}
