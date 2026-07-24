<?php

use LaraPkgs\Validation\Contracts\ValidatableFactory;
use LaraPkgs\Validation\ValidatableBuilder;
use LaraPkgs\Validation\ValidatableCollection;

if (! function_exists('validatable')) {
    /**
     * Create a new validatable instance or resolve the factory.
     */
    function validatable(null|string|ValidatableBuilder $first = null, ValidatableBuilder ...$rest): ValidatableCollection|ValidatableBuilder|ValidatableFactory
    {
        $factory = app(ValidatableFactory::class);

        return $first !== null
            ? $factory->make($first, ...$rest)
            : $factory;
    }
}
