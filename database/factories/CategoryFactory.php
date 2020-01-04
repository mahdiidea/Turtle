<?php

use Faker\Generator as Faker;

$factory->define(\App\Category::class, function (Faker $faker) {
    return [
        'caption' => $faker->text(rand(25, 50)),
        'content' => $faker->text,
        'picture' => getRandomIdentIcons(),
        'created_at' => $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now'),
        'updated_at' => $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now')
    ];
});
