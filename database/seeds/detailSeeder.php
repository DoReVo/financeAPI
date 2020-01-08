<?php

use App\Detail;
use App\Transaction;
use Illuminate\Database\Seeder;

class detailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $transaction = new Transaction;
        $transaction = $transaction->all();

        $transactionList = $transaction->pluck('id');

        $detail = factory(Detail::class, count($transactionList))->make();

        foreach ($detail as $key => $value) {
            $detail[$key]->save();
        }
    }
}
