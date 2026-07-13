<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

use LaraPkgs\Validation\Concerns\HasFluentRules;

final class HasFluentRuleTestClass
{
    use HasFluentRules;

    protected array $rules = [];

    public function getRules(): array
    {
        return $this->rules;
    }

    protected function applyFluentRule(string $ruleName, array $arguments = []): self
    {
        $this->rules[$ruleName] = $arguments;

        return $this;
    }
}