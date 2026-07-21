<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

use LaraPkgs\Validation\Concerns\HasFluentRules;
use LaraPkgs\Validation\Contracts\ValidationRule;

final class HasFluentRuleTestClass
{
    use HasFluentRules;

    protected array $rules = [];

    public function getRules(): array
    {
        return $this->rules;
    }

    protected function applyFluentRule(string|ValidationRule $rule, array $arguments = []): self
    {
        $this->rules[$rule] = $arguments;

        return $this;
    }
}