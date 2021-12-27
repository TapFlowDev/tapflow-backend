<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Null_;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('user_id');
            $table->integer('company_id')->nullable();
            $table->integer('team_id')->nullable();
            $table->string('name','255');
            $table->integer('budget_type');
            $table->string('budget');
            $table->text('description');
            $table->text('requirements_description');
            $table->string('days')->nullable();
            $table->integer('deleted')->default('0');
            $table->integer('status')->default('0');//0=>pending 1=>in progress 2=>completed 3=>rejected
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
        Schema::dropIfExists('projects');
    }
}
