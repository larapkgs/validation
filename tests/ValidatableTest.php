<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\Validatable as ValidatableContract;
use LaraPkgs\Validation\Validatable;
use LaraPkgs\Validation\ValidatableCollection;
use LaraPkgs\Validation\ValidatableBuilder;

beforeEach(function () {
    $this->validatable = new class() extends Validatable {
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
        $getValidatableCollection = function($subject) {
            return (fn() => $this->validatableCollection)->call($subject);
        };

        expect($this->validatable->getValidatableCollection())
            ->not->toBe($getValidatableCollection($this->validatable));
    });
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
        expect(fn() => $this->validatable->validate($data))
            ->toThrow(ValidationException::class);
    });
});

describe('Validatable::makeValidator', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        expect($this->validatable)->makeValidator([])
            ->toBeInstanceOf(Validator::class);
    });
});