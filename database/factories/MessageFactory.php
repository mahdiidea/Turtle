<?php

use Faker\Generator as Faker;

$factory->define(\App\Message::class, function (Faker $faker) {

    $participant = \App\Participant::query()->inRandomOrder()->first();

    return [
        'thread_id' => $participant->thread_id,
        'user_id' => $participant->user_id,
        'body' => $faker->text(rand(5, 512)),
        'created_at' => $faker->dateTimeBetween($startDate = '-1 months', $endDate = '+1 months'),
        'updated_at' => $faker->dateTimeBetween($startDate = '-1 months', $endDate = '+1 months')
    ];
});
