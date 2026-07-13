<?php

declare(strict_types=1);

use LaraPkgs\Validation\Contracts\RuleTypeResolver;
use LaraPkgs\Validation\Rules\RulePriorityResolver;

describe("RulePriorityResolver::resolve", function () {
    beforeEach(function () {
        $ruleTypeResolver = App::make(RuleTypeResolver::class);
        $this->resolver = new RulePriorityResolver($ruleTypeResolver);
    });

    it('it resolves known validation rules to a rule type', function (string $rule, int $priority) {
        expect($this->resolver->resolve($rule))->toBe($priority);
    })->with([
        'modifier rule' => ['sometimes', 1],
        'circuit rule'  => ['bail', 2],
        'presence rule' => ['required', 3],
        'type rule'     => ['integer', 4],
    ]);

    it('resolves to 100 when trying to resolve the priority for an unknown rule', function () {
        expect($this->resolver->resolve('unknown_rule'))->toBe(100);
    });
});