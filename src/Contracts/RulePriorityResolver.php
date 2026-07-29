<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Contracts;

interface RulePriorityResolver
{
    public function resolve(string $type): int;
}
