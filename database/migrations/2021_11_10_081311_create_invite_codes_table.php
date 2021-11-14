<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInviteCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invite_codes', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('code',100)->unique();
            $table->string('link_token',255)->unique();
            $table->integer('user_id');
            $table->integer('group_id');
            $table->integer('expired')->default(0);
            $table->string('email',255);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invite_codes');
    }
}
