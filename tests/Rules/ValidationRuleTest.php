<?php

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use LaraPkgs\Validation\Rules\ValidationRule;

it('expects a name on instantiation', function () {
    $rule = new ValidationRule('required');

    expect($rule)->toBeInstanceOf(ValidationRule::class);
});

it('accepts an array of arguments on instantiation', function () {
    $rule = new ValidationRule('min', $arguments = [1]);

    expect($rule)->getArguments()->toBe($arguments);
});

it('accepts an array of named arguments on instantiation', function () {
    $rule = new ValidationRule('min', $arguments = ['value' => 1]);

    expect($rule)->getArguments()->toBe($arguments);
});

it('defaults the arguments to an empty array', function () {
    $rule = new ValidationRule('required');

    expect($rule)->getArguments()->toBeEmpty();
});

it('accepts an integer indicating the priority on instantiation', function () {
    $rule = new ValidationRule('required', priority: 1);

    expect($rule)->getPriority()->toBe(1);
});

it('defaults the priority to 100', function () {
    $rule = new ValidationRule('required');

    expect($rule)->getPriority()->toBe(100);
});

describe('ValidationRule::getName', function () {
    it('provides the name of the rule', function () {
        $rule = new ValidationRule('required');

        expect($rule)->getName()->toBe('required');
    });
});

describe('ValidationRule::getAttributes', function () {
    it('provides the arguments for the rule', function () {
        $rule = new ValidationRule('between', $arguments = ['min' => 1, 'max' => 10]);

        expect($rule)->getArguments()->toBe($arguments);
    });
});

describe('ValidationRule::getPriority', function () {
    it('provides the priority of the rule', function () {
        $rule = new ValidationRule('required', priority: 5);

        expect($rule)->getPriority()->toBe(5);
    });
});

describe('ValidationRule::asValidatorRule', function () {
    it('provides the name when no arguments are set', function () {
        $rule = new ValidationRule('required');

        expect($rule)->asValidatorRule()->toBe('required');
    });

    it('provides an object when the only argument is an object', function () {
        $rule = new ValidationRule('in', [$object = Rule::in([])]);

        expect($rule)->asValidatorRule()->toBe($object);
    });

    it('provides a string starting with the name separated by colon from the comma separated arguments', function () {
        $rule = new ValidationRule('min', $arguments = ['value' => 1]);
        expect($rule)->asValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));

        $rule = new ValidationRule('min', $arguments = [1]);
        expect($rule)->asValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));

        $rule = new ValidationRule('between', $arguments = ['min' => 1, 'max' => 10]);
        expect($rule)->asValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));

        $rule = new ValidationRule('dimensions', $arguments = ['min_ratio=1/2','max_ratio=3/2']);
        expect($rule)->asValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));
    });
});