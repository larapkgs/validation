<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\Validatable as ValidatableContract;

abstract class Validatable implements ValidatableContract
{
    use IsValidatable;

    abstract protected function makeValidationCollection(): ValidationCollection;
}