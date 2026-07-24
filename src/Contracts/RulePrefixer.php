<?php

namespace LaraPkgs\Validation\Contracts;

interface RulePrefixer
{
    public function prefix(ValidationRule $rule, string $prefix): ValidationRule;
}
