<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreelancersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freelancers', function (Blueprint $table) {
            $table->integer('user_id')->unique();
          
            $table->text('bio')->nullable();
            $table->string('hourly_rate','255')->nullable();
            $table->string('experience','255')->nullable();
            $table->string('image','255')->nullable();
            $table->string('country','255')->nullable();
            $table->text('tools')->nullable();
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
        Schema::dropIfExists('freelancers');
    }
}
