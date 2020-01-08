<?php

use App\Item;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'item_name' => $faker->word ,
        'item_amount'=> $faker->randomDigit,
        'unit_price'=> $faker->randomDigit,
    ];
});
