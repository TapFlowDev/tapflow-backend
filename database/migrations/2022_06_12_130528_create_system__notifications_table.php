<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('title','255')->nullable();
            $table->text('body')->nullable();
            $table->string('action','255')->nullable();
            $table->integer('action_id')->nullable();
            $table->integer('receiver_id')->nullable();
            $table->string('link','255')->nullable();
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
        Schema::dropIfExists('system_notifications');
    }
}
