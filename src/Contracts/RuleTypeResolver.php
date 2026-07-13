<?php

namespace LaraPkgs\Validation\Contracts;

interface RuleTypeResolver
{
    public function resolve(string $ruleName): string;
}