<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMilestoneSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestone_submissions', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('milestone_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->text('links')->nullable();
            $table->string('file')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('milestone_submissions');
    }
}
