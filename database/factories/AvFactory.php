<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Av::class, function (Faker $faker) {
    $time = $faker->dateTimeThisMonth();
    return [
        'name' => $faker->sentence,
        'description' => $faker->realText(),
        'created_at' => $time,
        'updated_at' => $time,
    ];
});
