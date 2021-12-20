<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Library;

$factory->define(Library::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'slug' => $faker->unique()->slug,
    ];
});
