<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMilestoneSubmissionsAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('milestone_submissions', function (Blueprint $table) {
            $table->integer('status')->default(0);//2=>in review 3=>accepted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
