<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Support\ServiceProvider;
use LaraPkgs\Validation\Commands\MakeValidationCommand;
use LaraPkgs\Validation\Contracts\RulePriorityResolver as RulePriorityResolverContract;
use LaraPkgs\Validation\Contracts\RuleTypeResolver as RuleTypeResolverContract;
use LaraPkgs\Validation\Rules\RuleFactory;
use LaraPkgs\Validation\Rules\RulePriorityResolver;
use LaraPkgs\Validation\Rules\RuleStringParser;
use LaraPkgs\Validation\Rules\RuleTypeResolver;

final class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RuleParser::class, function ($app) {
            $parser  = $app->make(RuleStringParser::class);
            $factory = $app->make(RuleFactory::class);
            return new RuleParser($parser, $factory);
        });

        $this->app->singleton(RuleStringParser::class, function ($app) {
            $factory = $app->make(RuleFactory::class);
            return new RuleStringParser($factory);
        });

        $this->app->singleton(RuleTypeResolverContract::class, function ($app) {
            return new RuleTypeResolver();
        });

        $this->app->singleton(RulePriorityResolverContract::class, function ($app) {
            $ruleTypeResolver = $app->make(RuleTypeResolverContract::class);
            return new RulePriorityResolver($ruleTypeResolver);
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