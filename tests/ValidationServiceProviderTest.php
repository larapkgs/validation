<?php

use LaraPkgs\Validation\Contracts\RuleTypeResolver as RuleTypeResolverContract;
use LaraPkgs\Validation\Rules\RuleTypeResolver;

describe('ValidationServiceProvider::register', function() {
    it('binds the rule type resolver contract to the concrete implementation', function () {
        $resolved = app(RuleTypeResolverContract::class);

        expect($resolved)->toBeInstanceOf(RuleTypeResolver::class);
    });

    it('registers the rule type resolver as a singleton', function () {
        $resolver1 = app(RuleTypeResolverContract::class);
        $resolver2 = app(RuleTypeResolverContract::class);

        expect($resolver2)->toBe($resolver1);
    });
});