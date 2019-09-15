<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_transaction_detail', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');
            $table->string('detail', 255);
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
        Schema::dropIfExists('finance_transaction_detail');
    }
}
