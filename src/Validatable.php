<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Closure;
use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\ProvidesValidatableCollection;
use LaraPkgs\Validation\Contracts\Validatable as ValidatableContract;

abstract class Validatable implements ProvidesValidatableCollection, ValidatableContract
{
    use IsValidatable;

    protected ?ValidatableCollection $validatableCollection = null;

    public function __clone()
    {
        $this->validatableCollection = clone $this->resolveValidatableCollection();
    }

    protected function newInstance(Closure $callback): self
    {
        $instance = clone $this;

        return tap($instance, $callback);
    }

    public function getValidatableCollection(): ValidatableCollection
    {
        return clone $this->resolveValidatableCollection();
    }

    protected function resolveValidatableCollection(): ValidatableCollection
    {
        return $this->validatableCollection ??= $this->makeValidatableCollection();
    }

    public function merge(ValidatableCollection|ProvidesValidatableCollection ...$mergeables): self
    {
        return $this->newInstance(function (self $instance) use ($mergeables) {
            $instance->validatableCollection = $instance->resolveValidatableCollection()->merge(...$mergeables);
        });
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
