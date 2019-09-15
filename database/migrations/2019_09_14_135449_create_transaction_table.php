<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date_time');
            $table->bigInteger('category')->unsigned();
            $table->double('amount');
            $table->timestamp('record_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->softDeletes();

            $table->foreign('category')->references('id')->on('finance_category')->onUpdate('cascade');

            // $table->renameColumn('created_at', 'record_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_transaction');
    }
}
