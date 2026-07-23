<?php

declare(strict_types=1);

use Illuminate\Validation\Rule;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Contracts\RulePrefixer;
use LaraPkgs\Validation\Rules\RuleCollection;
use LaraPkgs\Validation\Rules\RuleParser;

it('expects an instance of the RuleFactory and RuleParser on instantiation', function () {
    $factory = App::make(RuleFactory::class);
    $parser = RuleParser::make();
    $prefixer = App::make(RulePrefixer::class);
    $collection = new RuleCollection($factory, $parser, $prefixer);

    expect($collection)->toBeInstanceOf(RuleCollection::class);
});

describe('applies fluent rules', function () {
    beforeEach(function () {
       $this->collection = RuleCollection::make();
    });

    it('applies rules without any arguments', function () {
        $collection = $this->collection->required();

        expect($collection)->not->toBe($this->collection)
            ->toValidatorArgument()->toBe(['required']);
    });

    it('applies rules that only have a single argument', function () {
        $collection = $this->collection->min(1);

        expect($collection)->not->toBe($this->collection)
            ->toValidatorArgument()->toBe(['min:1']);
    });

    it('applies rules that have multiple arguments', function () {
        $collection = $this->collection->between(1, 10);

        expect($collection)->not->toBe($this->collection)
            ->toValidatorArgument()->toBe(['between:1,10']);
    });

    it('applies rules that only have variadic arguments', function () {
        $collection = $this->collection->contains('category1', 'category2');

        expect($collection)->not->toBe($this->collection)
            ->toValidatorArgument()->toBe(['contains:category1,category2']);
    });

    it('applies rules that have positional and variadic arguments', function () {
        $collection = $this->collection->requiredIf('category', 'category1', 'category3');

        expect($collection)->not->toBe($this->collection)
            ->toValidatorArgument()->toBe(['required_if:category,category1,category3']);
    });

    it('allows chaining of fluent rules', function () {
        $collection = $this->collection->required()->min(10);

        expect($collection)->not->toBe($this->collection)
            ->toValidatorArgument()->toBe(['required', 'min:10']);
    });
});

describe('RuleCollection::make', function () {
    it('provides a factory method', function () {
        $collection = RuleCollection::make();

        expect($collection)->toBeInstanceOf(RuleCollection::class);
    });
});

describe('RuleCollection::prefix', function () {

    beforeEach(function () {
        $this->collection = RuleCollection::make();
    });

    it('prefixes eligible rules with a singular "field" argument', function () {
        $collection = $this->collection->requiredIf('category', 'category1', 'category3');
        expect($collection->toValidatorArgument())->toBe(['required_if:category,category1,category3']);

        $prefixed = $collection->prefix('items.*.');

        expect($prefixed)->not->toBe($collection)
            ->toValidatorArgument()->toBe(['required_if:items.*.category,category1,category3']);
    });

    it('prefixes eligible rules with a variadic/array "fields" argument', function () {
        $collection = $this->collection->requiredWith('field1', 'field3');
        expect($collection->toValidatorArgument())->toBe(['required_with:field1,field3']);

        $prefixed = $collection->prefix('items.*.');

        expect($prefixed)->not->toBe($collection)
            ->toValidatorArgument()->toBe(['required_with:items.*.field1,items.*.field3']);
    });

    it('leaves rules without prefixable arguments untouched', function () {
        $collection = $this->collection->between(1, 10);
        expect($collection->toValidatorArgument())->toBe(['between:1,10']);

        $prefixed = $collection->prefix('items.*.');

        expect($prefixed)->not->toBe($collection)
            ->toValidatorArgument()->toBe(['between:1,10']);
    });

    it('automatically appends a dot to the prefix if it is missing', function () {
        $collection = $this->collection->requiredIf('category', 'category1', 'category3');
        expect($collection->toValidatorArgument())->toBe(['required_if:category,category1,category3']);

        $prefixed = $collection->prefix('items.*');

        expect($prefixed)->not->toBe($collection)
            ->toValidatorArgument()->toBe(['required_if:items.*.category,category1,category3']);
    });
});

describe('RuleCollection::has', function () {
    it('indicates if a rule has been set', function () {
        $collection = RuleCollection::make()->required()->min(10)->applyRule($object = Rule::string());

        expect($collection->has('required'))->toBeTrue()
            ->and($collection->has('min'))->toBeTrue()
            ->and($collection->has($object::class))->toBeTrue()
            ->and($collection->has('max'))->toBeFalse();
    });
});

describe('RuleCollection::forget', function () {
    it('deletes rules', function () {
        $collection = RuleCollection::make()->required()->min(10);
        expect($collection->has('min'))->toBeTrue();

        expect($collection->forget('min'))
            ->not->toBe($collection)
            ->has('required')->toBeTrue()
            ->has('min')->toBeFalse();
    });
});

describe('RuleCollection::isEmpty', function () {
    it('indicates if no rules are set', function () {
        $collection = RuleCollection::make();
        expect($collection->isEmpty())->toBeTrue();

        expect($collection->required())->isEmpty()->toBeFalse();
    });
});

describe('RuleCollection::toValidatorArgument', function () {
    it('provides an array of rules compatible with Laravel’s validator', function () {
        $collection = RuleCollection::make()->required()->min(10)->max(100);

        expect($collection->toValidatorArgument())->toBe(['required', 'min:10', 'max:100']);
    });

    it('respects rule precedence', function () {
        $collection = RuleCollection::make()
            ->between(1, 10)->bail()->nullable()
            ->required()->integer();

        expect($collection->toValidatorArgument())->toBe(['nullable', 'bail', 'required', 'integer', 'between:1,10']);
    });
});

describe('RuleCollection::count', function () {
    it('is countable', function () {
        $collection = RuleCollection::make()->required()->min(10)->max(100);

        expect($collection)->toHaveCount(3);
    });
});
