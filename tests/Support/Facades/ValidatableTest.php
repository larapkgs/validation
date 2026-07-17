<?php

use LaraPkgs\Validation\Contracts\ValidatableFactory;
use LaraPkgs\Validation\Support\Facades\Validatable;

it('resolves the ValidatorFactory from the container', function () {
    expect(Validatable::getFacadeRoot())->toBeInstanceOf(ValidatableFactory::class);
});
