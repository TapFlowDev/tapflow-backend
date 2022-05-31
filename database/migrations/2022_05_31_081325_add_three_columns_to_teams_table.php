<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThreeColumnsToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('judgment', '255')->after('lead_time')->nullable();
            $table->string('years_of_experience', '255')->after('judgment')->nullable();
            $table->string('response_time', '255')->after('years_of_experience')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('judgment');
            $table->dropColumn('years_of_experience');
            $table->dropColumn('response_time');
        });
    }
}
