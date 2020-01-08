<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('categorySeeder');
        $this->call('transactionSeeder');
        $this->call('detailSeeder');
        $this->call('itemSeeder');
    }
}
