<?php

use App\Category;
use Illuminate\Database\Seeder;

class categorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $category = factory(Category::class, 5)->make();

        foreach ($category as $key => $value) {
            // $category[$key]->timestamps = false;
            $category[$key]->save();
        }

    }
}
