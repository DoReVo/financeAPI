<?php

use App\Detail;
use Faker\Generator as Faker;

$factory->define(Detail::class, function (Faker $faker) {
    return [
        'detail' => $faker->sentence($nbWords = 5, $variableNbWords = true) ,
    ];
});
