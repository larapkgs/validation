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
        $ruleName = $rule;

        if($rule instanceof ValidationRule) {
            $ruleName = $rule->getName();
            $arguments = $rule;
        }

        $this->rules[$ruleName] = $arguments;

        return $this;
    }
}