<?php

use LaraPkgs\Validation\Rules\RuleFactory;
use LaraPkgs\Validation\Rules\ValidationRule;

describe('RuleFactory::make', function () {
    it('creates a ValidationRule object', function () {
        $rule = new RuleFactory()->make('between', $arguments = ['min' => 1, 'max' => 10]);

        expect($rule)
            ->toBeInstanceOf(ValidationRule::class)
            ->getArguments()->toBe($arguments);
    });
});