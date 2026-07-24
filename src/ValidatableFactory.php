<?php

namespace LaraPkgs\Validation;

use LaraPkgs\Validation\Contracts\ValidatableFactory as ValidatableFactoryContract;

class ValidatableFactory implements ValidatableFactoryContract
{
    public function make(ValidatableBuilder|string $first, ValidatableBuilder ...$rest): ValidatableCollection|ValidatableBuilder
    {
        return $first instanceof ValidatableBuilder
            ? $this->collection($first, ...$rest)
            : $this->item($first);
    }

    public function item(string $key): ValidatableBuilder
    {
        return ValidatableBuilder::make($key);
    }

    public function collection(ValidatableBuilder ...$items): ValidatableCollection
    {
        return ValidatableCollection::make(...$items);
    }
}
