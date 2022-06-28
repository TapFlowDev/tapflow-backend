<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHireDeveloperFinalProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hire_developer_final_proposals', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('proposal_id');
            $table->integer('team_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->date('starting_date')->nullable();
            $table->integer('notice_period')->nullable();
            $table->integer('resource_replacment')->nullable();
            $table->integer('trail_period')->nullable();
            $table->integer('payment_settlement')->nullable();
            $table->text('additional_terms')->nullable();
            $table->integer('default_terms')->default(0);
            $table->integer('status')->default(0);
            $table->integer('deleted')->default(0);
            $table->integer('type')->default(0);
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
        Schema::dropIfExists('hire_developer_final_proposals');
    }
}
