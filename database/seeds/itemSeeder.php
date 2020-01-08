<?php

use App\Item;
use App\Transaction;
use Illuminate\Database\Seeder;

class itemSeeder extends Seeder
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

        // $item = factory(Item::class, count($transactionList))->make();

        foreach ($transactionList as $key => $idValue) {
            // Create 1 to 5 item
            $item = factory(Item::class, random_int(1, 5))->make();
            // Assign to current transaction
            foreach ($item as $itemKey => $value) {
                $item[$itemKey]->transaction_id = $idValue;
                $item[$itemKey]->save();
            }
        }
    }
}
