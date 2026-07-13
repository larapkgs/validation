<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use LaraPkgs\Validation\Contracts\RuleFactory as RuleFactoryContract;

final class RuleFactory implements RuleFactoryContract
{
    /**
     * @param array<array-key, mixed> $arguments
     */
    public function make(string $name, array $arguments = []): ValidationRule
    {
        return new ValidationRule($name, $arguments);
    }
}