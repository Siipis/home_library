<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Book;

$factory->define(Book::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'hash' => uniqid(),
        'authors' => $faker->name,
        'publisher' => $faker->company,
        'description' => $faker->paragraph,
        'year' => $faker->year,
    ];
});
