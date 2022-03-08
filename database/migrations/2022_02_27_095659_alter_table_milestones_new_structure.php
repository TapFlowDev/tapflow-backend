<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMilestonesNewStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('milestones', function (Blueprint $table) {
           
            $table->renameColumn('days','hours')->nullable();
            $table->dropColumn('percentage');
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
        Schema::table('milestones', function (Blueprint $table) {
        $table->dropColumn('hours');
    });
    }
}
