<?php

use Faker\Generator as Faker;
use Madewithlove\FeatureFlags\Models\FeatureFlag;

$factory->define(FeatureFlag::class, function (Faker $faker) {
    return [
        'flag' => strtoupper($faker->word),
        'value' => $faker->boolean,
        'description' => $faker->sentence,
    ];
});

$factory->state(FeatureFlag::class, 'disabled', function () {
    return [
        'value' => false,
    ];
});
