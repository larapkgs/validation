<?php

namespace LaraPkgs\Validation\Contracts;

interface RulePriorityResolver
{
    public function resolve(string $rule): int;
}