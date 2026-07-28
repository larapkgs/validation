<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

use LaraPkgs\Validation\Validatable;
use LaraPkgs\Validation\ValidatableCollection;

final class TestValidation extends Validatable
{
    protected function makeValidatableCollection(): ValidatableCollection
    {
        return validatable(
            validatable('property')->required()
        );
    }
}
