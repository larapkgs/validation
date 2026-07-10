<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Support\ServiceProvider;
use LaraPkgs\Validation\Commands\MakeValidationCommand;

final class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/validation.php', 'validation'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/validation.php' => config_path('validation.php'),
            ], 'larapkgs-validation-config');


            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs'),
            ], 'larapkgs-validation-stubs');

            $this->commands([
                MakeValidationCommand::class,
            ]);
        }
    }
}