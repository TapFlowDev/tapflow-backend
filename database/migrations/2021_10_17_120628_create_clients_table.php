<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->integer('user_id')->unique();
            $table->integer('company_id')->nullable();
            $table->text('bio')->nullable();
            $table->string('experience','255')->nullable();
            $table->string('attachment','255')->nullable();
            $table->string('image','255')->nullable();
            $table->string('country','255')->nullable();
            $table->string('role','255')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
