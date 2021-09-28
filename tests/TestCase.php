<?php

declare(strict_types=1);

namespace JustSteveKing\Transporter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use JustSteveKing\Transporter\TransporterServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            TransporterServiceProvider::class,
        ];
    }
}
