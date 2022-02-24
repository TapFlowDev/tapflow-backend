<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablelMilestonesCoulmnNullable extends Migration
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
           
            $table->string('name')->nullable()->change();
            $table->string('description')->nullable()->change();
            $table->string('percentage')->nullable()->change();
            $table->string('days')->nullable()->change();
          
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
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('percentage');
            $table->dropColumn('days');
        });
    }
}
