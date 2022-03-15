<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsInNotificationsSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_settings', function (Blueprint $table) {
            $table->integer('has_group_name')->default(0)->after('notification_text');
            $table->unique('email_template');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications_settings', function (Blueprint $table) {
            //
        });
    }
}
