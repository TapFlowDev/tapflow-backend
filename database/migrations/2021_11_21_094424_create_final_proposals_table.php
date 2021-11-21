<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_proposals', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('propsal_id');
            $table->integer('team_id');
            $table->integer('project_id');
            $table->string('price_min','255');
            $table->string('price_max','255');
            $table->text('description')->nullable();
            $table->string('days','255');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('final_proposals');
    }
}
