<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\ProvidesValidatableCollection;
use LaraPkgs\Validation\Contracts\Validatable as ValidatableContract;
use LaraPkgs\Validation\Validatable;
use LaraPkgs\Validation\ValidatableBuilder;
use LaraPkgs\Validation\ValidatableCollection;

function getValidatableCollection($subject)
{
    return (fn () => $this->resolveValidatableCollection())->call($subject);
}

beforeEach(function () {
    $this->validatable = new class extends Validatable
    {
        protected function makeValidatableCollection(): ValidatableCollection
        {
            return ValidatableCollection::make(
                ValidatableBuilder::make('property1')->required(),
                ValidatableBuilder::make('property2')->nullable(),
            );
        }
    };
});

it('implements the Validatable Contract', function () {
    expect($this->validatable)
        ->toBeInstanceOf(Validatable::class)
        ->toBeInstanceOf(ValidatableContract::class);
});

it('uses the IsValidatable trait', function () {
    expect(class_uses(Validatable::class))->toHaveKey(IsValidatable::class);
});

describe('Validatable::getValidatableCollection', function () {
    it('provides a clone of the underlying ValidatableCollection', function () {
        $getValidatableCollection = function ($subject) {
            return (fn () => $this->validatableCollection)->call($subject);
        };

        expect($this->validatable->getValidatableCollection())
            ->not->toBe($getValidatableCollection($this->validatable));
    });
});

describe('Validatable::merge', function () {
    it('merges a variadic list of validation collections and returns a new instance', function () {
        $collection = ValidatableCollection::make(
            ValidatableBuilder::make('merged')->required(),
        );

        expect($this->validatable->getValidatableCollection())
            ->getItems()->keys()->all()->toBe(['property1', 'property2']);

        $merged = $this->validatable->merge($collection);

        expect(getValidatableCollection($merged))
            ->not->toBe(getValidatableCollection($this->validatable));

        expect($merged)
            ->not->toBe($this->validatable)
            ->getValidatableCollection()
            ->getItems()->keys()->all()->toBe(['property1', 'property2', 'merged']);
    });

    it('allows to merge classes that implement the ProvidesValidatableCollection interface', function () {
        $classThatProvidesValidatableCollection = new class implements ProvidesValidatableCollection
        {
            public function getValidatableCollection(): ValidatableCollection
            {
                return ValidatableCollection::make(
                    ValidatableBuilder::make('merged')->required(),
                );
            }
        };

        expect($this->validatable->getValidatableCollection())
            ->getItems()->keys()->all()->toBe(['property1', 'property2']);

        $merged = $this->validatable->merge($classThatProvidesValidatableCollection);

        expect($merged)
            ->not->toBe($this->validatable)
            ->getValidatableCollection()
            ->getItems()->keys()->all()->toBe(['property1', 'property2', 'merged']);
    });
});

describe('Validatable::prefix', function () {
    it('applies a prefix to the keys of the underlying ValidatableCollection and returns a new instance', function (string $prefix) {
        $prefixed = $this->validatable->prefix($prefix);

        expect($prefixed)
            ->not->toBe($this->validatable)
            ->getValidatableCollection()
            ->getItems()->keys()->all()->toBe([
                'collection.*.property1',
                'collection.*.property2',
            ]);
    })->with(['collection.*', 'collection.*.']);
});

describe('Validatable::passes', function () {
    it('indicates if the given data passes the constraints as set by the collection', function () {
        $data = ['property1' => 'value1', 'property2' => 'value2'];

        expect($this->validatable)->passes($data)->toBeTrue();
    });
});

describe('Validatable::fails', function () {
    it('indicates if the given data fails the constraints as set by the collection', function () {
        $data = [];

        expect($this->validatable)->fails($data)->toBeTrue();
    });
});

describe('Validatable::validate', function () {
    it('tries to validate the given data', function () {
        $data = ['property1' => 'value1', 'property2' => 'value2'];
        expect($this->validatable)->validate($data)->toBe($data);

        $data = [];
        expect(fn () => $this->validatable->validate($data))
            ->toThrow(ValidationException::class);
    });
});

describe('Validatable::makeValidator', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        expect($this->validatable)->makeValidator([])
            ->toBeInstanceOf(Validator::class);
    });
});
