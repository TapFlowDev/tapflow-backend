<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFinalProposalAddStartdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('final_proposals', function (Blueprint $table) {
            $table->string('starting_date')->nullable()->after('days');
            $table->string('title')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('final_proposals', function (Blueprint $table) {
            $table->dropColumn('starting_date');
            $table->dropColumn('title');
        });
    }
}
