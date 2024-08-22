<?php

declare(strict_types=1);

namespace App\Tests\Concerns;

use Faker\Factory;
use Faker\Generator;

trait WithFaker
{
    private ?Generator $faker = null;

    private function faker(string $locale = Factory::DEFAULT_LOCALE): Generator
    {
        if ($this->faker === null) {
            $this->faker = Factory::create($locale);
        }

        return $this->faker;
    }
}
