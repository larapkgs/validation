<?php

namespace LaraPkgs\Validation\Contracts;

use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\ValidationCollection;

interface Validatable
{
    public function getValidationCollection(): ValidationCollection;
    public function makeValidator(array $data): Validator;
    public function validate(array $data, ?string $errorBagPrefix = null): array;
}