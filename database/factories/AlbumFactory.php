<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Album::class, function (Faker $faker) {
    $time = $faker->dateTimeThisMonth();
    return [
        'name' => $faker->sentence,
        'description' => $faker->text,
        'created_at' => $time,
        'updated_at' => $time,
    ];
});
