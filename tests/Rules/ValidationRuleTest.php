<?php

declare(strict_types=1);

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

it('deep clones the arguments', function () {
    $rule = new ValidationRule('string_rule', $arguments = ['object' => Rule::string()]);

    $cloned = clone $rule;
    $clonedArguments = $cloned->getArguments();

    expect($cloned)
        ->not->toBe($rule)
        ->and($clonedArguments['object'])->not->toBe($arguments['object']);
});

describe('ValidationRule::getName()', function () {
    it('provides the name of the rule', function () {
        $rule = new ValidationRule('required');

        expect($rule)->getName()->toBe('required');
    });
});

describe('ValidationRule::withArguments()', function () {
    it('merges the given arguments and returns a new instance', function () {
        $rule = new ValidationRule('required_if', $arguments = ['field' => 'category', 'values' => ['category1', 'category3']]);
        expect($rule)->getArguments()->toBe($arguments);

        $arguments = ['field' => 'items.*.category', 'values' => ['category1', 'category3']];
        $merged = $rule->withArguments($arguments);

        expect($merged)
            ->not->toBe($rule)
            ->getArguments()->toBe($arguments);
    });
});

describe('ValidationRule::getArguments()', function () {
    it('provides the arguments for the rule', function () {
        $rule = new ValidationRule('between', $arguments = ['min' => 1, 'max' => 10]);

        expect($rule)->getArguments()->toBe($arguments);
    });
});

describe('ValidationRule::getPriority()', function () {
    it('provides the priority of the rule', function () {
        $rule = new ValidationRule('required', priority: 5);

        expect($rule)->getPriority()->toBe(5);
    });
});

describe('ValidationRule::toValidatorRuleUsing()', function () {
    it('accepts a closure as a custom validator rule resolver', function () {
        $resolver = function (ValidationRule $rule) {
            return 'custom_min:' . implode(',', $rule->getArguments());
        };

        $rule = new ValidationRule('min', ['value' => 5]);
        $customRule = $rule->toValidatorRuleUsing($resolver);

        expect($rule->toValidatorRule())->toBe('min:5')
            ->and($customRule->toValidatorRule())->toBe('custom_min:5');
    });

    it('accepts a callable as a custom validator rule resolver', function () {
        $resolver = new class
        {
            public function __invoke(ValidationRule $rule): string
            {
                return 'custom_min:' . implode(',', $rule->getArguments());
            }
        };

        $rule = new ValidationRule('min', ['value' => 5]);
        $customRule = $rule->toValidatorRuleUsing($resolver);

        expect($rule->toValidatorRule())->toBe('min:5')
            ->and($customRule->toValidatorRule())->toBe('custom_min:5');
    });
});

describe('ValidationRule::toValidatorRule()', function () {
    it('provides the name when no arguments are set', function () {
        $rule = new ValidationRule('required');

        expect($rule)->toValidatorRule()->toBe('required');
    });

    it('provides an object when the only argument is an object', function () {
        $rule = new ValidationRule('in', [$object = Rule::in([])]);

        expect($rule->toValidatorRule())
            ->not->toBe($object)
            ->toEqual($object);
    });

    it('provides a string starting with the name separated by colon from the comma separated arguments', function () {
        $rule = new ValidationRule('min', $arguments = ['value' => 1]);
        expect($rule)->toValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));

        $rule = new ValidationRule('min', $arguments = [1]);
        expect($rule)->toValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));

        $rule = new ValidationRule('between', $arguments = ['min' => 1, 'max' => 10]);
        expect($rule)->toValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));

        $rule = new ValidationRule('dimensions', $arguments = ['min_ratio=1/2', 'max_ratio=3/2']);
        expect($rule)->toValidatorRule()->toBe($rule->getName() . ':' . Arr::join($arguments, ','));

        $rule = new ValidationRule('accepted_if', $arguments = [
            'anotherField' => 'category',
            'values' => ['category1', 'category3', 'category5'],
        ]);
        expect($rule)->toValidatorRule()->toBe('accepted_if:category,category1,category3,category5');
    });

    it('filters out empty array arguments', function () {
        $rule = new ValidationRule('email', ['validators' => []]);

        expect($rule)->toValidatorRule()->toBe('email');
    });
});
