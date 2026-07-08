<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\ValidationCollection;
use LaraPkgs\Validation\ValidationItem;

function getValidationCollection(object $subject) {
    return (fn() => $this->validationCollection)->call($subject);
}

beforeEach(function () {
    $this->validation = new class() {
        use IsValidatable;

        protected function makeValidationCollection(): ValidationCollection
        {
            return ValidationCollection::make(
                new ValidationItem('property1', 'required'),
                new ValidationItem('property2', 'nullable'),
            );
        }
    };
});

it('lazily resolves the validationCollection using the abstract makeValidationCollection method', function () {
    expect(getValidationCollection($this->validation))->toBeNull();

    expect($this->validation->getValidationCollection())->toBeInstanceOf(ValidationCollection::class);
});

describe('IsValidatable::getValidationCollection', function () {
    it('provides a cloned instance of the underlying ValidationCollection', function () {
        $collection = $this->validation->getValidationCollection();

        expect($collection)
            ->toBeInstanceOf(ValidationCollection::class)
            ->not->toBe(getValidationCollection($this->validation));
    });
});

describe('IsValidatable::makeValidator', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        expect($this->validation)->makeValidator([])->toBeInstanceOf(ValidatorContract::class);
    });
});

describe('IsValidatable::validate', function () {
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