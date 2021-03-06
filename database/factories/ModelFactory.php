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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Sportsrecord::class,function(Faker\Generator $faker){
   return[
       'emotion' => $faker->word,
       'content' => join("\n\n", $faker->paragraphs(mt_rand(3,6))),
       'format' => $faker->word,
       'duration' => $faker->word,
       'published_at' => $faker->dateTimeBetween('-1 month','+3 days'),
       'comType' => 'single'
   ] ;
});

$factory->define(App\Competition::class, function ($faker) {
    return [
        'name' => $faker->sentence(mt_rand(3, 10)),
        'comType' => 'single',
        'description' => join("\n\n", $faker->paragraphs(mt_rand(3, 6))),
        'endtime' => $faker->dateTimeBetween('-1 month', '+3 days'),
    ];
});