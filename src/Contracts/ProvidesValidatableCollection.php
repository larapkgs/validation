<?php

namespace LaraPkgs\Validation\Contracts;

use LaraPkgs\Validation\ValidatableCollection;

interface ProvidesValidatableCollection
{
    public function getValidatableCollection(): ValidatableCollection;
}
