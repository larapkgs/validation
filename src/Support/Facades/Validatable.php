<?php

namespace LaraPkgs\Validation\Support\Facades;

use Illuminate\Support\Facades\Facade;
use LaraPkgs\Validation\Contracts\ValidatableFactory;
use LaraPkgs\Validation\ValidatableBuilder;
use LaraPkgs\Validation\ValidatableCollection;

/**
 * @method static ValidatableCollection|ValidatableBuilder make(string|ValidatableBuilder $first, ValidatableBuilder ...$rest)
 * @method static ValidatableBuilder item(string $key)
 * @method static ValidatableCollection collection(ValidatableBuilder ...$items)
 *
 * @see ValidatableFactory
 */
class Validatable extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return ValidatableFactory::class;
    }
}
