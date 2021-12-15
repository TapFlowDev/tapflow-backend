<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('project_transaction_it');
            $table->integer('company_id');
            $table->integer('agency_id');
            $table->integer('transaction_price');
            $table->integer('tapflow_fee');
            $table->integer('total_price');
            $table->integer('type');
            $table->integer('stats')->deflate(0);
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
        Schema::dropIfExists('transactions');
    }
}
