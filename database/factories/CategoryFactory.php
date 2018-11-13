<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->randomElement(['镜头特效','图像处理','个人作品']),
        'description' => $faker->sentence,
    ];
});
