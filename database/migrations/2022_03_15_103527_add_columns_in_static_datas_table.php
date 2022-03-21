<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInStaticDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('static_datas', function (Blueprint $table) {
            $table->text('title')->after('id')->nullable();
            $table->text('big_text')->after('text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('static_datas', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('big_text');
        });
    }
}
