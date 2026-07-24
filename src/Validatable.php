<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\Validatable as ValidatableContract;

abstract class Validatable implements ValidatableContract
{
    use IsValidatable;

    protected ?ValidatableCollection $validatableCollection = null;

    public function getValidatableCollection(): ValidatableCollection
    {
        return clone $this->resolveValidatableCollection();
    }

    protected function resolveValidatableCollection(): ValidatableCollection
    {
        return clone $this->validatableCollection ??= $this->makeValidatableCollection();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function makeValidator(array $data): Validator
    {
        return $this->resolveValidatableCollection()->makeValidator($data);
    }

    abstract protected function makeValidatableCollection(): ValidatableCollection;
}
