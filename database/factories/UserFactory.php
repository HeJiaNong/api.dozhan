<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {

    //bcrypt 是一个消耗 cpu 的函数，用静态变量可以不用每次都去重新计算
    static $password;

    //获取当前时间并转换为字符串格式
    $now = \Carbon\Carbon::now()->toDateTimeString();

    return [
        'do_id' => uniqid('do_'),
        'name' => $faker->name,
        'introduction' => $faker->sentence,
        'avatar_url' => 'http://pglgpkuzs.bkt.clouddn.com/image/avatar/4.webp',
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
        'qq' => rand(1,9999999999),
        'password' => $password?:bcrypt('123456'), // secret
        'created_at' => $now,
        'updated_at' => $now,
    ];
});
