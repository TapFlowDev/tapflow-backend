<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFinarProposal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('final_proposals', function (Blueprint $table) {
           
            
            $table->renameColumn('hours','num_hours');
          
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
        Schema::table('final_proposals', function (Blueprint $table) {
           
            
            $table->dropColumn('num_hours');
          
        });
    }
}