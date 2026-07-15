<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Contracts;

use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\ValidationCollection;

interface Validatable
{
    public function getValidationCollection(): ValidationCollection;
    public function passes(array $data): bool;
    public function fails(array $data): bool;
    public function validate(array $data, ?string $errorBagPrefix = null): array;
    public function makeValidator(array $data): Validator;
}