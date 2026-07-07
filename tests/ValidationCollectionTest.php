<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\ValidationCollection;
use LaraPkgs\Validation\ValidationItem;

it('expects an instance of the Laravel Validation Factory contract on instantiation', function () {
    expect(fn() => new ValidationCollection())
        ->toThrow(ArgumentCountError::class);

    expect(fn() => new ValidationCollection(
        new ValidationItem('property', 'required', 'min:10|max:100')
    ))->toThrow(TypeError::class);

    $factory = app(Factory::class);
    expect(new ValidationCollection($factory))->toBeInstanceOf(ValidationCollection::class);
});

it('accepts a variadic list of items on instantiation', function () {
    $collection = new ValidationCollection(
        app(Factory::class),
        new ValidationItem('property1', 'required'),
        new ValidationItem('property2', 'required')
    );

    expect($collection)->toHaveCount(2);
});

describe('ValidationCollection::make', function () {
    it('provides a factory method', function () {
        $collection = ValidationCollection::make();

        expect($collection)->toBeInstanceOf(ValidationCollection::class);
    });
});

describe('ValidationCollection::add', function () {
    it('adds a variadic list of items and returns a new instance', function () {
        $collection = ValidationCollection::make();
        expect($collection)->toHaveCount(0);

        $mutated = $collection->add(
            new ValidationItem('property1', 'required')
        );

        expect($mutated)
            ->not->toBe($collection)
            ->and($mutated)->toHaveCount(1);
    });
});

describe('ValidationCollection::getItems', function () {
    it('provides ad instanceof the underlying items collection', function () {
        $collection = ValidationCollection::make(
            new ValidationItem('property1', 'required'),
        );

        $items1 = $collection->getItems();
        $items2 = $collection->getItems();

        expect($items1)->not->toBe($items2);
    });
});

describe('ValidationCollection::makeValidator', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        $collection = ValidationCollection::make(
            new ValidationItem('property1', 'required')
                ->messages(['required' => 'Custom :attribute required message.'])
                ->customAttribute('customized'),
        );

        $validator = $collection->makeValidator([]);

        expect($validator)
            ->toBeInstanceOf(Validator::class)
            ->errors()->first('property1')->toBe('Custom customized required message.');
    });
});

describe('ValidationCollection::toArray', function () {
    it('implements Arrayable and provides an array of rules, messages and attributes compatible with Laravel Validation', function () {
        $collection = ValidationCollection::make(
            new ValidationItem('property1', 'required')
                ->messages(['required' => 'The :attribute field is required.']),
            new ValidationItem('property2', 'required', 'min:10', 'max:100')
                ->customAttribute('custom2')
        );

        expect($collection)
            ->toBeInstanceOf(Arrayable::class)
            ->and($collection->toArray())
            ->toBe([
                'rules' => [
                    'property1' => ['required'],
                    'property2' => ['required', 'min:10', 'max:100'],
                ],
                'messages' => [
                    'property1.required' => 'The :attribute field is required.',
                ],
                'attributes' => [
                    'property1' => 'property1',
                    'property2' => 'custom2'
                ]
            ]);
    });

});

describe('ValidationCollection::count', function () {
    it('is countable', function () {
        $collection = ValidationCollection::make(
            new ValidationItem('property1', 'required'),
            new ValidationItem('property2', 'required', 'min:10', 'max:100')
        );

        expect($collection)->toHaveCount(2);
    });
});






