<?php

namespace LaraPkgs\Validation\Tests;

use LaraPkgs\Validation\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        //
    }

    protected function getPackageProviders($app): array
    {
        return [
            ValidationServiceProvider::class,
        ];
    }
}