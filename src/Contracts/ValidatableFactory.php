<?php

namespace LaraPkgs\Validation\Contracts;

use LaraPkgs\Validation\ValidatableCollection;
use LaraPkgs\Validation\ValidatableBuilder;

interface ValidatableFactory
{
    public function make(string|ValidatableBuilder $first, ValidatableBuilder ...$rest): ValidatableCollection|ValidatableBuilder;

    public function item(string $key): ValidatableBuilder;

    public function collection(ValidatableBuilder ...$items): ValidatableCollection;
}