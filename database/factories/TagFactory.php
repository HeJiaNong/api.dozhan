<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Tag::class, function (Faker $faker) {
    //随机取一个月以内的时间
    $time = $faker->dateTimeThisMonth();

    return [
        'name' => $faker->unique()->randomElement(['游戏','教程','吃鸡','LOL','特效','Ae','Pr','分享','学习','生活','社交','娱乐','正能量']),
        'description' => $faker->sentence,
        'created_at' => $time,
        'updated_at' => $time,
    ];
});
