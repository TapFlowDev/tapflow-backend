<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFinalProposalsDeleteUnnecessaryColumns extends Migration
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
            $table->dropColumn('hourly_rate');
            $table->dropColumn('down_payment');
            $table->dropColumn('down_payment_value');
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
           
        });
    }
}
