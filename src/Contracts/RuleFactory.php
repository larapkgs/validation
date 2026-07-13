<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Contracts;

use LaraPkgs\Validation\Rules\ValidationRule;

interface RuleFactory
{
    /**
     * @param array<array-key, mixed> $arguments
     */
    public function make(string $name, array $arguments = []): ValidationRule;
}