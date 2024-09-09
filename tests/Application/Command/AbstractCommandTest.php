<?php

declare(strict_types=1);

namespace Mirspay\Tests\Application\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractCommandTest extends KernelTestCase
{
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->application = new Application(self::$kernel);
    }
}
