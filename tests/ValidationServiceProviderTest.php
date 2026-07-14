<?php

declare(strict_types=1);

use LaraPkgs\Validation\Contracts\RulePrefixer as RulePrefixerContract;
use LaraPkgs\Validation\Contracts\RulePriorityResolver as RulePriorityResolverContract;
use LaraPkgs\Validation\Contracts\RuleTypeResolver as RuleTypeResolverContract;
use LaraPkgs\Validation\Rules\RulePrefixer;
use LaraPkgs\Validation\Rules\RulePriorityResolver;
use LaraPkgs\Validation\Rules\RuleTypeResolver;

describe('ValidationServiceProvider::register', function() {
    it('binds the RuleTypeResolver contract to the concrete implementation', function () {
        $resolved = app(RuleTypeResolverContract::class);

        expect($resolved)->toBeInstanceOf(RuleTypeResolver::class);
    });

    it('registers the RuleTypeResolver as a singleton', function () {
        $resolver1 = app(RuleTypeResolverContract::class);
        $resolver2 = app(RuleTypeResolverContract::class);

        expect($resolver2)->toBe($resolver1);
    });

    it('binds the RulePriorityResolver contract to the concrete implementation', function () {
        $resolved = app(RulePriorityResolverContract::class);

        expect($resolved)->toBeInstanceOf(RulePriorityResolver::class);
    });

    it('registers the RulePriorityResolver as a singleton', function () {
        $resolver1 = app(RulePriorityResolverContract::class);
        $resolver2 = app(RulePriorityResolverContract::class);

        expect($resolver2)->toBe($resolver1);
    });

    it('binds the RulePrefixer contract to the concrete implementation', function () {
        $resolved = app(RulePrefixerContract::class);

        expect($resolved)->toBeInstanceOf(RulePrefixer::class);
    });

    it('registers the RulePrefixer as a singleton', function () {
        $prefixer1 = app(RulePrefixerContract::class);
        $prefixer2 = app(RulePrefixerContract::class);

        expect($prefixer2)->toBe($prefixer1);
    });
});