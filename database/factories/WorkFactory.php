<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Work::class, function (Faker $faker) {
    $time = $faker->dateTimeThisMonth();
    return [
        'name' => $faker->sentence,
        'description' => $faker->realText(),
//        'comment_count' => mt_rand(0,999),
//        'favour_count' => mt_rand(0,99999),
        'created_at' => $time,
        'updated_at' => $time,
    ];
});
