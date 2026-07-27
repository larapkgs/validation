<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use LaraPkgs\Validation\Rules\RuleTypeResolver;

describe('RuleTypeResolver::resolve', function () {
    beforeEach(function () {
        $this->resolver = new RuleTypeResolver;
    });

    it('it resolves known validation rules to a RuleType', function (string $rule, string $type) {
        expect($this->resolver->resolve($rule))->toBe($type);
    })->with([
        'modifier rule' => ['sometimes', 'modifier'],
        'circuit rule' => ['bail', 'circuit'],
        'presence rule' => ['required', 'presence'],
        'type rule' => ['integer', 'type'],
    ]);

    it('resolves to constraint when trying to resolve an unknown rule', function () {
        expect($this->resolver->resolve('unknown_rule'))
            ->toBe('constraint');
    });

    it('resolves the default rule type from the config', function () {
        expect($this->resolver->resolve('unknown_rule'))
            ->toBe('constraint');

        Config::set('validation.default_rule_type', 'configured');

        expect($this->resolver->resolve('unknown_rule'))
            ->toBe('configured');
    });

    it('it internally caches the resolved ruleToTypeMap', function () {
        $getRuleToTypeMap = function () {
            return (fn () => $this->getRuleToTypeMap())->call($this->resolver);
        };

        $map1 = $getRuleToTypeMap();
        $map2 = $getRuleToTypeMap();

        expect($map1)->toBe($map2);
    });
});
