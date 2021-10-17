<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcceptedProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accepted_proposals', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('team_id');
            $table->integer('project_id');
            $table->integer('proposal_id');
            $table->string('price','255');
            $table->string('days','255');
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
        Schema::dropIfExists('accepted_proposals');
    }
}
