<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\ValidationCollection;
use LaraPkgs\Validation\ValidationItem;

beforeEach(function () {
    $this->validation = new class() {
        use IsValidatable;

        public function makeValidator(array $data): Validator
        {
            return ValidationCollection::make(
                ValidationItem::make('property1', 'required'),
                ValidationItem::make('property2', 'nullable'),
            )->makeValidator($data);
        }
    };
});

describe('IsValidatable::passes', function () {
    it('indicates if the given data passes the constraints as set by the rules', function () {
        $validation = ValidationItem::make('property')->required();
        $data = ['property' => 'value'];

        expect($validation)->passes($data)->toBeTrue();
    });
});

describe('IsValidatable::fails', function () {
    it('indicates if the given data fails the constraints as set by the rules', function () {
        $validation = ValidationItem::make('property')->required();
        $data = [];

        expect($validation)->fails($data)->toBeTrue();
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

describe('IsValidatable::makeValidator', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        expect($this->validation)->makeValidator([])->toBeInstanceOf(ValidatorContract::class);
    });
});