<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\Contracts\Validation as ValidationContract;
use LaraPkgs\Validation\BaseValidation;
use LaraPkgs\Validation\ValidationCollection;
use LaraPkgs\Validation\ValidationItem;

beforeEach(function () {
    $this->validation = new class() extends BaseValidation {
        protected function makeValidationCollection(): ValidationCollection
        {
            return ValidationCollection::make(
                new ValidationItem('property1', 'required'),
                new ValidationItem('property2', 'nullable'),
            );
        }
    };
});


it('implements the Validation interface', function () {
    expect($this->validation)->toBeInstanceOf(ValidationContract::class);
});

it('creates and instance of ValidationCollection based on the abstract getItems method', function () {
    expect($this->validation)->getValidationCollection()->toHaveCount(2);
});

describe('Validation::getValidationCollection', function () {
    it('provides a cloned instance of the underlying ValidationCollection', function () {
        $getValidationCollection = function (BaseValidation $subject) {
            return (fn() => $this->collection)->call($subject);
        };

        $collection = $this->validation->getValidationCollection();

        expect($collection)
            ->toBeInstanceOf(ValidationCollection::class)
            ->not->toBe($getValidationCollection($this->validation));
    });
});

describe('Validation::makeValidator', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        expect($this->validation)->makeValidator([])->toBeInstanceOf(ValidatorContract::class);
    });
});

describe('Validation::validate', function () {
    it('validates the given data', function () {
        expect(fn() => $this->validation->validate([]))
            ->toThrow(ValidationException::class);

        $data = ['property1' => 'value1', 'property2' => 'value2'];
        expect($this->validation->validate($data))->toBe($data);
    });

    it('optionally prefixes the errorBag keys with the given errorBag prefix', function () {
        $getValidationErrorKeys = function (?string $errorBagPrefix = null) {
            try {
                $this->validation->validate([], $errorBagPrefix);
            } catch(ValidationException $e) {
                return array_keys($e->errors());
            }
        };

        expect($getValidationErrorKeys())->toBe(['property1']);

        expect($getValidationErrorKeys('data.'))->toBe(['data.property1']);
    });
});