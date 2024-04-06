<?php declare(strict_types=1);

namespace Tests;

use ArtisanCommandApibResource\ApibResourceGenerateCommandServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ApibResourceGenerateCommandServiceProvider::class,
        ];
    }
}
