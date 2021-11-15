<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->integer('group_id')->unique();
            $table->text('bio')->nullable();
            $table->string('attachment')->nullable();
            $table->string('image')->nullable();
            $table->string('link');
            $table->string('country','255')->nullable();
            $table->string('employees_number','255')->nullable();
            $table->string('field','255')->nullable();
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
        Schema::dropIfExists('companies');
    }
}
