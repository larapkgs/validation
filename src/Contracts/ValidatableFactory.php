<?php

namespace LaraPkgs\Validation\Contracts;

use LaraPkgs\Validation\ValidatableBuilder;
use LaraPkgs\Validation\ValidatableCollection;

interface ValidatableFactory
{
    public function make(string|ValidatableBuilder $first, ValidatableBuilder ...$rest): ValidatableCollection|ValidatableBuilder;

    public function item(string $key): ValidatableBuilder;

    public function collection(ValidatableBuilder ...$items): ValidatableCollection;
}
