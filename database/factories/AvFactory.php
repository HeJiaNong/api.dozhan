<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Av::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence,
        'description' => $faker->realText(),
    ];
});
