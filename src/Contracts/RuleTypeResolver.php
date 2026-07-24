<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Contracts;

interface RuleTypeResolver
{
    public function resolve(string $ruleName): string;
}
