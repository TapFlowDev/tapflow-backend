<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMilestoneSubmitions extends Migration
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
            $table->renameColumn('comment', 'agency_comments')->nullable();
            $table->text('client_comments')->nullable();
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
        Schema::table('milestone_submissions', function (Blueprint $table) {
            $table->dropColumn('agency_comments');
            $table->dropColumn('client_comments');
        });
    }
}
