<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Comment::class, function (Faker $faker) {
    $time = $faker->dateTimeThisMonth();
    return [
        'comment' => $faker->sentence,
        'created_at' => $time,
        'updated_at' => $time,
    ];
});
