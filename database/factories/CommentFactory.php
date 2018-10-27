<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Comment::class, function (Faker $faker) {
    $time = $faker->dateTimeThisMonth();
    return [
        'content' => $faker->sentence,
//        'favour_count' => mt_rand(0,99999),
        'created_at' => $time,
        'updated_at' => $time,
    ];
});
