<?php

declare(strict_types=1);

use LaraPkgs\Validation\Contracts\ValidatableFactory;
use LaraPkgs\Validation\ValidatableBuilder;
use LaraPkgs\Validation\ValidatableCollection;

describe('validatable() helper', function () {
    it('instantiates a ValidatableBuilder when given a string', function () {
        expect(validatable('email'))->toBeInstanceOf(ValidatableBuilder::class);
    });

    it('instantiates a ValidatableCollection when given a ValidatableBuilder', function () {
        expect(validatable(
            ValidatableBuilder::make('email')->required(),
            ValidatableBuilder::make('password')->required()
        ))->toBeInstanceOf(ValidatableCollection::class);
    });

    it('returns the ValidatableFactory instance when called without arguments', function () {
        expect(validatable())->toBeInstanceOf(ValidatableFactory::class);
    });
});
