<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('type','255')->nullable();//agency type, 1 tema of freelancers, 2 is agency
            $table->text('bio')->nullable();
            $table->string('attachment')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->string('country','255')->nullable();
            $table->string('employees_number','255')->nullable();
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
        Schema::dropIfExists('teams');
    }
}
