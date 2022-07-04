<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_resources', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('team_id');
            $table->integer('user_id')->nullable();
             $table->string('name', 255);
            $table->integer('seniority');
            $table->integer('country');
             $table->string('hourly_rate', 255);
             $table->string('cv', 255)->nullable();
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
        Schema::dropIfExists('agency_resources');
    }
}
