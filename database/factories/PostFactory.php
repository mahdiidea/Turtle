<?php

use Faker\Generator as Faker;

$factory->define(\App\Post::class, function (Faker $faker) {
    return [
        'user_id' => \App\User::query()->inRandomOrder()->pluck('id')->first(),
        'body' => $faker->text(rand(50, 100)),
        'created_at' => $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now'),
        'updated_at' => $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now')
    ];
});
