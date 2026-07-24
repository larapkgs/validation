<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Support\ServiceProvider;
use LaraPkgs\Validation\Commands\MakeValidationCommand;
use LaraPkgs\Validation\Contracts\RuleFactory as RuleFactoryContract;
use LaraPkgs\Validation\Contracts\RulePrefixer as RulePrefixerContract;
use LaraPkgs\Validation\Contracts\RulePriorityResolver as RulePriorityResolverContract;
use LaraPkgs\Validation\Contracts\RuleTypeResolver as RuleTypeResolverContract;
use LaraPkgs\Validation\Contracts\ValidatableFactory as ValidatableFactoryContract;
use LaraPkgs\Validation\Rules\RuleFactory;
use LaraPkgs\Validation\Rules\RuleParser;
use LaraPkgs\Validation\Rules\RulePrefixer;
use LaraPkgs\Validation\Rules\RulePriorityResolver;
use LaraPkgs\Validation\Rules\RuleTypeResolver;

final class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RuleParser::class, function ($app) {
            $factory = $app->make(RuleFactoryContract::class);

            return new RuleParser($factory);
        });

        $this->app->singleton(RuleTypeResolverContract::class, function ($app) {
            return new RuleTypeResolver;
        });

        $this->app->singleton(RulePriorityResolverContract::class, function ($app) {
            $ruleTypeResolver = $app->make(RuleTypeResolverContract::class);

            return new RulePriorityResolver($ruleTypeResolver);
        });

        $this->app->singleton(RuleFactoryContract::class, function ($app) {
            $rulePriorityResolver = $app->make(RulePriorityResolverContract::class);

            return new RuleFactory($rulePriorityResolver);
        });

        $this->app->singleton(RulePrefixerContract::class, function ($app) {
            return new RulePrefixer;
        });

        $this->app->singleton(ValidatableFactoryContract::class, function ($app) {
            return new ValidatableFactory;
        });

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
