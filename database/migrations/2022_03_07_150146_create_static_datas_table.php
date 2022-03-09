<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_datas', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->text('image')->nullable();
            $table->text('link')->nullable();
            $table->text('text')->nullable();
            $table->integer('type')->default(0);//1->agency ,2->client
            $table->integer('hidden')->default(0);//0->show,1->hide
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
        Schema::dropIfExists('static_datas');
    }
}
