<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToTableFinalProposalType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('final_proposals', function (Blueprint $table) {
            //
            $table->string('type')->default(0);//1 regular milestones , 2 => monthly
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
            //
            $table->dropColumn('type');
        });
    }
}
