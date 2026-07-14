<?php

declare(strict_types=1);

use Illuminate\Validation\Rule;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Contracts\RulePrefixer;
use LaraPkgs\Validation\RuleCollection;
use LaraPkgs\Validation\RuleParser;
use LaraPkgs\Validation\Rules\ValidationRule;

it('expects an instance of the RuleFactory and RuleParser on instantiation', function () {
    $factory = App::make(RuleFactory::class);
    $parser = RuleParser::make();
    $prefixer = App::make(RulePrefixer::class);
    $collection = new RuleCollection($factory, $parser, $prefixer);

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

describe('applies fluent rules', function () {
    beforeEach(function () {
       $this->collection = RuleCollection::make();
    });

    it('applies rules without any arguments', function () {
        expect($this->collection)->required()
            ->toBe($this->collection)
            ->toArray()->toBe(['required']);
    });

    it('applies rules that only have a single argument', function () {
        expect($this->collection)->min(1)
            ->toBe($this->collection)
            ->toArray()->toBe(['min:1']);
    });

    it('applies rules that have multiple arguments', function () {
        expect($this->collection)->between(1,10)
            ->toBe($this->collection)
            ->toArray()->toBe(['between:1,10']);
    });

    it('applies rules that only have variadic arguments', function () {
        expect($this->collection)->contains('category1', 'category2')
            ->toBe($this->collection)
            ->toArray()->toBe(['contains:category1,category2']);
    });

    it('applies rules that have positional and variadic arguments', function () {
        expect($this->collection)->requiredIf('category', 'category1', 'category3')
            ->toBe($this->collection)
            ->toArray()->toBe(['required_if:category,category1,category3']);
    });

    it('allows chaining of fluent rules', function () {
        expect($this->collection)->required()->min(10)
            ->toBe($this->collection)
            ->toArray()->toBe(['required', 'min:10']);
    });
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

describe('RuleCollection::prefix', function () {

    beforeEach(function () {
        $this->rules = RuleCollection::make();
    });

    it('prefixes eligible rules with a singular "field" argument', function () {
        $this->rules->requiredIf('category', 'category1', 'category3');
        expect($this->rules->toArray())->toBe(['required_if:category,category1,category3']);

        $prefixed = $this->rules->prefix('items.*.');

        expect($prefixed)->toBe($this->rules)
            ->toArray()->toBe(['required_if:items.*.category,category1,category3']);
    });

    it('prefixes eligible rules with a variadic/array "fields" argument', function () {
        $this->rules->requiredWith('field1', 'field3');
        expect($this->rules->toArray())->toBe(['required_with:field1,field3']);

        $prefixed = $this->rules->prefix('items.*.');

        expect($prefixed)->toBe($this->rules)
            ->toArray()->toBe(['required_with:items.*.field1,items.*.field3']);
    });

    it('leaves rules without prefixable arguments untouched', function () {
        $this->rules->between(1, 10);
        expect($this->rules->toArray())->toBe(['between:1,10']);

        $prefixed = $this->rules->prefix('items.*.');

        expect($prefixed)->toBe($this->rules)
            ->toArray()->toBe(['between:1,10']);
    });

    it('automatically appends a dot to the prefix if it is missing', function () {
        $this->rules->requiredIf('category', 'category1', 'category3');
        expect($this->rules->toArray())->toBe(['required_if:category,category1,category3']);

        $prefixed = $this->rules->prefix('items.*');

        expect($prefixed)->toBe($this->rules)
            ->toArray()->toBe(['required_if:items.*.category,category1,category3']);
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

    it('respects rule precedence', function () {
        $rules = RuleCollection::make()
            ->between(1, 10)->bail()->nullable()
            ->required()->integer();

        expect($rules->toArray())->toBe(['nullable', 'bail', 'required', 'integer', 'between:1,10']);
    });
});

describe('RuleCollection::count', function () {
    it('is countable', function () {
        $rules = RuleCollection::make('required|min:10|max:100');

        expect($rules)->toHaveCount(3);
    });
});
