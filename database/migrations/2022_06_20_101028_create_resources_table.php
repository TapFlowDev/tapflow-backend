<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('contract_id');
            $table->string('name', 255)->nullable();
            $table->string('job_function', 255)->nullable();
            $table->integer('duration')->nullable();
            $table->integer('hours')->nullable();
            $table->string('rate', 255)->nullable();
            $table->date('starting_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('image', 255)->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('status')->default(0);
            $table->integer('deleted')->default(0);
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
        Schema::dropIfExists('resources');
    }
}
