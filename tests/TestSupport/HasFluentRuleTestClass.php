<?php

namespace LaraPkgs\Validation\Tests\TestSupport;

use LaraPkgs\Validation\Concerns\HasFluentRules;

class HasFluentRuleTestClass
{
    use HasFluentRules;

    protected array $rules = [];

    public function getRules(): array
    {
        return $this->rules;
    }

    protected function applyFluentRule(string $rule, array $arguments = []): self
    {
        $this->rules[$rule] = $arguments;

        return $this;
    }
}