<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->randomElement(['镜头特效','工具技巧','软件教程','专辑系列','游戏解说']),
        'cover' => $faker->imageUrl(200,200),
        'description' => $faker->sentence,
    ];
});
