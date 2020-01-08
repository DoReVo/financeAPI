<?php

use App\Category;
use App\Transaction;
use Illuminate\Database\Seeder;

class transactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transaction = factory(Transaction::class, 10)->make();

        $category = new Category;
        $category = $category->all();

        // $categoryList = array();
        // foreach ($category as $key => $value) {
        //     array_push($categoryList, $category[$key]['id']);
        // }

        $categoryList = $category->pluck('id');

        foreach ($transaction as $key => $value) {
            $transaction[$key]->category = $categoryList[random_int(0, count($categoryList) - 1)];
            $transaction[$key]->save();
        }
    }
}
