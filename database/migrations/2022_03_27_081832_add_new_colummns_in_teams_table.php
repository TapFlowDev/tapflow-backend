<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColummnsInTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('min_work_hour', '255')->after('maxPerHour')->nullable();
            $table->string('max_work_hour', '255')->after('min_work_hour')->nullable();
            $table->string('lead_time', '255')->after('max_work_hour')->nullable();
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
            $table->dropColumn('min_work_hour');
            $table->dropColumn('max_work_hour');
            $table->dropColumn('lead_time');
        });
    }
}
