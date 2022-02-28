<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFinalProposlasNewStructure extends Migration
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
           
            $table->renameColumn('days','hours')->nullable();
            $table->string('down_payment_value')->nullable()->after('down_payment');
            $table->string('user_id')->nullable()->after('project_id');
            $table->string('hourly_rate')->nullable()->after('days');
          
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
           
            $table->dropColumn('days','hours');
            $table->dropColumn('down_payment_value');
            $table->dropColumn('user_id');
            $table->dropColumn('hourly_rate');
          
        });
    }
}
