<?php

declare(strict_types=1);

use LaraPkgs\Validation\Rules\RulePrefixer;
use LaraPkgs\Validation\Rules\ValidationRule;

describe('RulePrefixer::prefix()', function () {

    beforeEach(function () {
        $this->prefixer = new RulePrefixer;
    });

    it('prefixes singular "field" arguments and returns a new ValidationRule instance', function () {
        $rule = new ValidationRule('required_if', ['field' => 'category', 'values' => ['category1', 'category3']]);

        $prefixed = $this->prefixer->prefix($rule, 'items.*.');

        expect($prefixed)
            ->not->toBe($rule)
            ->getArguments()->toBe(['field' => 'items.*.category', 'values' => ['category1', 'category3']]);
    });

    it('prefixes variadic/array "fields" arguments and return a new ValidationRule instance', function () {
        $rule = new ValidationRule('required_with', ['fields' => ['field1', 'field3']]);

        $prefixed = $this->prefixer->prefix($rule, 'items.*.');

        expect($prefixed)
            ->not->toBe($rule)
            ->getArguments()->toBe(['fields' => ['items.*.field1', 'items.*.field3']]);
    });

    it('leaves rules without prefixable arguments untouched', function () {
        $rule = new ValidationRule('between', $arguments = ['min' => 1, 'max' => 10]);

        $prefixed = $this->prefixer->prefix($rule, 'items.*.');

        expect($prefixed)->toBe($rule)
            ->getArguments()->toBe($arguments);
    });

    it('automatically appends a dot to the prefix if it is missing', function () {
        $rule = new ValidationRule('required_if', ['field' => 'category', 'values' => ['category1']]);

        $prefixed = $this->prefixer->prefix($rule, 'items.*');

        expect($prefixed)
            ->getArguments()->toBe(['field' => 'items.*.category', 'values' => ['category1']]);
    });
});
