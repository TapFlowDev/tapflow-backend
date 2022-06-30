<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeReplacementHireDeveloperFinalProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hire_developer_final_proposals', function (Blueprint $table) {
            //
            $table->renameColumn("resource_replacment","resource_replacement");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hire_developer_final_proposals', function (Blueprint $table) {
            //
            $table->renameColumn("resource_replacement", "resource_replacment");
        });
    }
}
