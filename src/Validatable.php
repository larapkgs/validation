<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\Validatable as ValidatableContract;

abstract class Validatable implements ValidatableContract
{
    use IsValidatable;

    protected ?ValidationCollection $validationCollection = null;

    public function getValidationCollection(): ValidationCollection
    {
        return clone $this->resolveValidationCollection();
    }

    protected function resolveValidationCollection(): ValidationCollection
    {
        return clone $this->validationCollection ??= $this->makeValidationCollection();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function makeValidator(array $data): Validator
    {
        return $this->resolveValidationCollection()->makeValidator($data);
    }

    abstract protected function makeValidationCollection(): ValidationCollection;
}