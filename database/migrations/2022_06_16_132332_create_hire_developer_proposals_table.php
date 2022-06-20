<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHireDeveloperProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hire_developer_proposals', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('project_id');
            $table->integer('team_id');
            $table->integer('user_id');
             $table->string('details', 255)->nullable();
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
        Schema::dropIfExists('hire_developer_proposals');
    }
}
