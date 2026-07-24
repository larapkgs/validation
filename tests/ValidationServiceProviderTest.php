<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Contracts\RulePrefixer as RulePrefixerContract;
use LaraPkgs\Validation\Contracts\RulePriorityResolver as RulePriorityResolverContract;
use LaraPkgs\Validation\Contracts\RuleTypeResolver as RuleTypeResolverContract;
use LaraPkgs\Validation\Contracts\ValidatableFactory as ValidatableFactoryContract;
use LaraPkgs\Validation\Rules\RulePrefixer;
use LaraPkgs\Validation\Rules\RulePriorityResolver;
use LaraPkgs\Validation\Rules\RuleTypeResolver;
use LaraPkgs\Validation\ValidatableFactory;

describe('ValidationServiceProvider::register', function () {
    it('binds the RuleTypeResolver contract to the concrete implementation', function () {
        $resolved = App::make(RuleTypeResolverContract::class);

        expect($resolved)->toBeInstanceOf(RuleTypeResolver::class);
    });

    it('registers the RuleTypeResolver as a singleton', function () {
        $resolver1 = App::make(RuleTypeResolverContract::class);
        $resolver2 = App::make(RuleTypeResolverContract::class);

        expect($resolver2)->toBe($resolver1);
    });

    it('binds the RulePriorityResolver contract to the concrete implementation', function () {
        $resolved = App::make(RulePriorityResolverContract::class);

        expect($resolved)->toBeInstanceOf(RulePriorityResolver::class);
    });

    it('registers the RulePriorityResolver as a singleton', function () {
        $resolver1 = App::make(RulePriorityResolverContract::class);
        $resolver2 = App::make(RulePriorityResolverContract::class);

        expect($resolver2)->toBe($resolver1);
    });

    it('binds the RulePrefixer contract to the concrete implementation', function () {
        $resolved = App::make(RulePrefixerContract::class);

        expect($resolved)->toBeInstanceOf(RulePrefixer::class);
    });

    it('registers the RulePrefixer as a singleton', function () {
        $prefixer1 = App::make(RulePrefixerContract::class);
        $prefixer2 = App::make(RulePrefixerContract::class);

        expect($prefixer2)->toBe($prefixer1);
    });

    it('binds the ValidatableFactory contract to the concrete implementation', function () {
        $resolved = App::make(ValidatableFactoryContract::class);

        expect($resolved)->toBeInstanceOf(ValidatableFactory::class);
    });

    it('registers the ValidatableFactory as a singleton', function () {
        $factory1 = App::make(ValidatableFactoryContract::class);
        $factory2 = App::make(ValidatableFactoryContract::class);

        expect($factory2)->toBe($factory1);
    });
});
