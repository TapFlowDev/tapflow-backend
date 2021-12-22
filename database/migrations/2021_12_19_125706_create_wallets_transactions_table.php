<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets_transactions', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('wallet_id');
            $table->integer('amount');
            $table->integer('type'); //1=>deposit 2=>withdraw
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
        Schema::dropIfExists('wallets_transactions');
    }
}
