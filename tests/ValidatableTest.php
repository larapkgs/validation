<?php

use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\Validatable as ValidatableContract;
use LaraPkgs\Validation\Validatable;
use LaraPkgs\Validation\ValidationCollection;
use LaraPkgs\Validation\ValidationItem;

beforeEach(function () {
    $this->validation = new class() extends Validatable {
        protected function makeValidationCollection(): ValidationCollection
        {
            return ValidationCollection::make(
                new ValidationItem('property1', 'required'),
                new ValidationItem('property2', 'nullable'),
            );
        }
    };
});

it('implements the Validatable Contract', function () {
    expect($this->validation)
        ->toBeInstanceOf(Validatable::class)
        ->toBeInstanceOf(ValidatableContract::class);
});

it('uses the IsValidatable trait', function () {
    expect(class_uses(Validatable::class))->toHaveKey(IsValidatable::class);
});
