<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteCoulmnRequirementsDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('requirements_description');

        });
    }

    /**_
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
