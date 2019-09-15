<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_transaction_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('transaction_id')->unsigned();
            $table->string('item_name', 255);
            $table->integer('item_amount');
            $table->double('unit_price');
            $table->timestamp('record_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->softDeletes();

            $table->foreign('transaction_id')->references('id')->on('finance_transaction')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_transaction_item');
    }
}
