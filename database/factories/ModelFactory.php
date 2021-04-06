<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name'                => $faker->name,
        'email'               => $faker->unique()->safeEmail,
        'phone'               => '+61' . $faker->randomNumber(9, true),
        'password'            => $password ?: 'password',
        'remember_token'      => str_random(10),
        'avatar'              => 'https://placeimg.com/100/100/animals?'.$faker->randomNumber(6, true),
    ];
});
