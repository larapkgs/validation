<?php

namespace LaraPkgs\Validation\Tests\TestSupport;

use LaraPkgs\Validation\Validatable;
use LaraPkgs\Validation\ValidatableCollection;

class TestValidation extends Validatable
{
    protected function makeValidatableCollection(): ValidatableCollection
    {
        return validatable(
            validatable('property')->required()
        );
    }
}