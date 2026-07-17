<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\ValidatableCollection;
use LaraPkgs\Validation\ValidatableBuilder;

it('expects an instance of the Laravel Validation Factory contract on instantiation', function () {
    $newCollectionWithoutValidationFactory = function(string ...$rules) {
        /**
         * @noinspection PhpParamsInspection
         * @noinspection RedundantSuppression
         */
        return new ValidatableCollection(...$rules);
    };

    expect(fn() => $newCollectionWithoutValidationFactory())
        ->toThrow(ArgumentCountError::class);

    expect(fn() => $newCollectionWithoutValidationFactory(...['property', 'required', 'min:10|max:100']))
        ->toThrow(TypeError::class);

    $factory = app(Factory::class);
    expect(new ValidatableCollection($factory))->toBeInstanceOf(ValidatableCollection::class);
});

it('accepts a variadic list of items on instantiation', function () {
    $collection = new ValidatableCollection(
        app(Factory::class),
        ValidatableBuilder::make('property1', 'required'),
        ValidatableBuilder::make('property2', 'required')
    );

    expect($collection)->toHaveCount(2);
});

describe('ValidatableCollection::make', function () {
    it('provides a factory method', function () {
        $collection = ValidatableCollection::make();

        expect($collection)->toBeInstanceOf(ValidatableCollection::class);
    });
});

describe('ValidatableCollection::add', function () {
    it('adds a variadic list of items and returns a new instance', function () {
        $collection = ValidatableCollection::make();
        expect($collection)->toHaveCount(0);

        $mutated = $collection->add(
            ValidatableBuilder::make('property1', 'required')
        );

        expect($mutated)
            ->not->toBe($collection)
            ->and($mutated)->toHaveCount(1);
    });
});

describe('ValidatableCollection::prefix', function () {
    it('applies a prefix to the keys of all items and returns a new instance', function () {
        $validation = ValidatableCollection::make(
            ValidatableBuilder::make('property1', 'required'),
            ValidatableBuilder::make('property2', 'required'),
        );

        $prefixed = $validation->prefix('collection.*.');

        expect($prefixed)
            ->not->toBe($validation)
            ->getItems()->keys()->all()->toBe([
                'collection.*.property1',
                'collection.*.property2',
            ]);
    });
});

describe('ValidatableCollection::merge', function () {
    it('merges a variadic list of validation collections and returns a new instance', function () {
        $getItem = function(ValidatableCollection $collection, string $key) {
            return (fn() => $this->items)->call($collection)->get($key);
        };

        $validation = ValidatableCollection::make(
            $item1 = ValidatableBuilder::make('property1', 'required'),
        );

        $mergeable1 = ValidatableCollection::make(
            $item2 = ValidatableBuilder::make('property2', 'required'),
        );

        $mergeable2 = ValidatableCollection::make(
            $item3 = ValidatableBuilder::make('property3', 'required'),
        );

        $merged = $validation->merge($mergeable1, $mergeable2);

        expect($merged)
            ->not->toBe($validation)
            ->getItems()->keys()->all()->toBe(['property1', 'property2', 'property3']);

        expect($item1)->not->toBe($getItem($merged, 'property1'))
            ->and($item2)->not->toBe($getItem($merged, 'property2'))
            ->and($item3)->not->toBe($getItem($merged, 'property3'));
    });
});

describe('ValidatableCollection::getItems', function () {
    it('provides ad instanceof the underlying items collection', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('property1', 'required'),
        );

        $items1 = $collection->getItems();
        $items2 = $collection->getItems();

        expect($items1)->not->toBe($items2);
    });
});

describe('ValidatableCollection::passes', function () {
    it('indicates if the given data passes the constraints as set by the items', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('property1')->required(),
            ValidatableBuilder::make('property2')->required()
        );

        $data = ['property1' => 'value1', 'property2' => 'value2'];

        expect($collection)->passes($data)->toBeTrue();
    });
});

describe('ValidatableCollection::fails', function () {
    it('indicates if the given data fails the constraints as set by the rules', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('property1')->required(),
            ValidatableBuilder::make('property2')->required()
        );

        $data = ['property1' => 'value1'];

        expect($collection)->fails($data)->toBeTrue();
    });
});

describe('ValidatableCollection::validate', function () {
    it('tries to validate the given data', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('property1')->required(),
            ValidatableBuilder::make('property2')->required()
        );
        $data = ['property1' => 'value1', 'property2' => 'value2'];

        expect($collection)->validate($data)->toBe($data);

        $data = ['property1' => 'value1'];

        expect(fn() => $collection->validate($data))
            ->toThrow(ValidationException::class);
    });
});

describe('ValidatableCollection::toValidatorArguments', function () {
    it('provides an array of arguments compatible with the Laravel Validator Factory', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('property1', 'required')
                ->addMessages(['required' => 'The :attribute field is required.']),
            ValidatableBuilder::make('property2', 'required', 'min:10', 'max:100')
                ->setCustomAttribute('custom2')
        );

        expect($collection)
            ->and($collection->toValidatorArguments())
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

describe('ValidatableCollection::makeValidator', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('property1', 'required')
                ->addMessages(['required' => 'Custom :attribute required message.'])
                ->setCustomAttribute('customized'),
        );

        $validator = $collection->makeValidator([]);

        expect($validator)
            ->toBeInstanceOf(Validator::class)
            ->errors()->first('property1')->toBe('Custom customized required message.');
    });
});

describe('ValidatableCollection::count', function () {
    it('is countable', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('property1', 'required'),
            ValidatableBuilder::make('property2', 'required', 'min:10', 'max:100')
        );

        expect($collection)->toHaveCount(2);
    });
});