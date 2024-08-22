<?php

declare(strict_types=1);

namespace App\Tests\Concerns;

trait WithFixtureLoader
{
    private function loadFixture(string $filename): string
    {
        $fixtureFile = __DIR__ . '/../fixtures/' . $filename;

        if (! file_exists($fixtureFile)) {
            throw new \RuntimeException("Fixture file '{$fixtureFile}' does not exist.");
        }

        return file_get_contents($fixtureFile);
    }

    private function loadJsonFixture(string $filename): mixed
    {
        return json_decode($this->loadFixture($filename), associative: true, flags: JSON_THROW_ON_ERROR);
    }
}
