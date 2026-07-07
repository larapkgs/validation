<?php

declare(strict_types=1);

use Illuminate\Validation\Rule;
use LaraPkgs\Validation\RuleCollection;

it('accepts rules on instantiation', function () {
    $rules = new RuleCollection('required|min:10', 'max:100', $object = Rule::string());

    expect($rules->toArray())->toBe(['required', 'min:10', 'max:100', $object]);
});

describe('RuleCollection::add', function () {
    it('adds rules', function () {
        $rules = new RuleCollection();

        $rules->add('required|min:10')->add('max:100')->add($object = Rule::string());

        expect($rules)->toHaveCount(4);
    });

    it('makes the nullable and required rule mutually exclusive', function () {
        $rules = new RuleCollection('required');
        expect($rules->has('required'))->toBeTrue()
            ->and($rules->has('nullable'))->toBeFalse();

        $rules->add('nullable');
        expect($rules->has('nullable'))->toBeTrue()
            ->and($rules->has('required'))->toBeFalse();

        $rules->add('required');
        expect($rules->has('required'))->toBeTrue()
            ->and($rules->has('nullable'))->toBeFalse();
    });
});

describe('RuleCollection::has', function () {
    it('indicates if a rule has been set', function () {
        $rules = new RuleCollection('required|min:10', $object = Rule::string());

        expect($rules->has('required'))->toBeTrue()
            ->and($rules->has('min'))->toBeTrue()
            ->and($rules->has(get_class($object)))->toBeTrue()
            ->and($rules->has('max'))->toBeFalse();
    });
});

describe('RuleCollection::forget', function () {
    it('deletes rules', function () {
        $rules = new RuleCollection('required|min:10');
        expect($rules->has('min'))->toBeTrue();

        $rules->forget('min');
        expect($rules->has('required'))->toBeTrue()
            ->and($rules->has('min'))->toBeFalse();
    });
});

describe('RuleCollection::isEmpty', function () {
    it('indicates if no rules are set', function () {
        $rules = new RuleCollection();
        expect($rules->isEmpty())->toBeTrue();

        $rules->add('required');
        expect($rules->isEmpty())->toBeFalse();
    });
});

describe('RuleCollection::toArray', function () {
    it('implements Arrayable and provides an array of validation rules compatible with Laravel’s validator', function () {
        $expected = ['required', 'min:10', 'max:100', $object = Rule::string()];
        $rules = new RuleCollection($expected);

        expect($rules->toArray())->toBe($expected);
    });

    it('respects rule order precedence', function (string $rule) {
        $rules = new RuleCollection(['min:10', $rule]);

        expect($rules->toArray())->toBe([$rule, 'min:10']);
    })->with(['required', 'nullable']);
});

describe('RuleCollection::count', function () {
    it('is countable', function () {
        $rules = new RuleCollection('required', 'min:10');

        expect($rules)->toHaveCount(2);
    });
});
