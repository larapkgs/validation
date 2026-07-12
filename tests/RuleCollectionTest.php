<?php

declare(strict_types=1);

use Illuminate\Validation\Rule;
use LaraPkgs\Validation\RuleCollection;
use LaraPkgs\Validation\RuleParser;
use LaraPkgs\Validation\Rules\ValidationRule;

it('expects an instance or RuleParser on instantiation', function () {
    $parser = RuleParser::make();
    $collection = new RuleCollection($parser);

    expect($collection)->toBeInstanceOf(RuleCollection::class);
});

it('accepts variadic list parsable rules on instantiation', function () {
    $rules = RuleCollection::make(
        'required',
        ['min:1', 'max:10'],
        new ValidationRule('between', ['min' => 1, 'max' => 10]),
        $object = Rule::string()
    );

    expect($rules->toArray())->toBe(['required', 'min:1', 'max:10', 'between:1,10', $object]);
});

describe('RuleCollection::make', function () {
    it('provides a factory method that accepts a variadic list of parsable rules', function () {
        $rules = RuleCollection::make('required|between:1,10');

        expect($rules->toArray())->toBe(['required', 'between:1,10']);
    });
});

describe('RuleCollection::add', function () {
    it('adds a variadic list of parsable rules', function () {
        $rules = RuleCollection::make();
        $rules->add('required|min:10')->add('max:100')->add($object = Rule::string());

        expect($rules->toArray())->toBe(['required', 'min:10', 'max:100', $object]);
    });
});

describe('RuleCollection::has', function () {
    it('indicates if a rule has been set', function () {
        $rules = RuleCollection::make('required|min:10', $object = Rule::string());

        expect($rules->has('required'))->toBeTrue()
            ->and($rules->has('min'))->toBeTrue()
            ->and($rules->has($object::class))->toBeTrue()
            ->and($rules->has('max'))->toBeFalse();
    });
});

describe('RuleCollection::forget', function () {
    it('deletes rules', function () {
        $rules = RuleCollection::make('required|min:10');
        expect($rules->has('min'))->toBeTrue();

        $rules->forget('min');
        expect($rules->has('required'))->toBeTrue()
            ->and($rules->has('min'))->toBeFalse();
    });
});

describe('RuleCollection::isEmpty', function () {
    it('indicates if no rules are set', function () {
        $rules = RuleCollection::make();
        expect($rules->isEmpty())->toBeTrue();

        $rules->add('required');
        expect($rules->isEmpty())->toBeFalse();
    });
});

describe('RuleCollection::toArray', function () {
    it('implements Arrayable and provides an array of rules compatible with Laravel’s validator', function () {
        $expected = ['required|min:10|max:100', $object = Rule::string()];
        $rules = RuleCollection::make($expected);

        expect($rules->toArray())->toBe(['required', 'min:10', 'max:100', $object]);
    });
});

describe('RuleCollection::count', function () {
    it('is countable', function () {
        $rules = RuleCollection::make('required|min:10|max:100');

        expect($rules)->toHaveCount(3);
    });
});
