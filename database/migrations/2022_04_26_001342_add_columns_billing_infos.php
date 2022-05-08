<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsBillingInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_infos', function (Blueprint $table) {
            $table->string('legal_name', '255');
            $table->string('phone', '255');
            $table->string('legal_address', '255');
            $table->string('building', '255')->nullable();
            $table->string('city', '255');
            $table->string('region', '255');
            $table->string('zip_code', '255');
            $table->string('SWIFT', '255');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_infos', function (Blueprint $table) {
            $table->dropColumn('legal_name');
            $table->dropColumn('legal_address');
            $table->dropColumn('building');
            $table->dropColumn('city');
            $table->dropColumn('region');
            $table->dropColumn('zip_code');
            $table->dropColumn('SWIFT');
            
        });
    }
}
