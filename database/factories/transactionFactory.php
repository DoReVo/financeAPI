<?php

use App\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'date_time' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'amount' => $faker->randomDigit,
    ];
});
