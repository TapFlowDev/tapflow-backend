<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFinalProposalMakeColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('final_proposals', function (Blueprint $table) {
           
            $table->string('days')->nullable()->change();
            $table->string('price')->nullable()->change();
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
            $table->dropColumn('days');
            $table->dropColumn('price');
        });
    }
}
