<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Contracts\RulePriorityResolver;
use LaraPkgs\Validation\Rules\RuleFactory;
use LaraPkgs\Validation\Rules\ValidationRule;

describe('RuleFactory::make()', function () {
    beforeEach(function () {
        $rulePriorityResolver = App::make(RulePriorityResolver::class);
        $this->factory = new RuleFactory($rulePriorityResolver);
    });

    dataset('rules', function () {
        return [
            'modifier rule' => ['sometimes', [], 1],
            'circuit rule' => ['bail', [], 2],
            'presence rule' => ['required', [], 3],
            'type rule' => ['integer', [], 4],
            'constraint rule' => ['min', ['value' => 1], 100],
        ];
    });

    it('creates a ValidationRule object respecting rule precedence', function (string $name, array $arguments, int $priority) {
        $rule = $this->factory->make($name, $arguments);

        expect($rule)
            ->toBeInstanceOf(ValidationRule::class)
            ->getArguments()->toBe($arguments);
    })->with('rules');
});
