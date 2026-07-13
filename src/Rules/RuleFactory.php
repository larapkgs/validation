<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use LaraPkgs\Validation\Contracts\RuleFactory as RuleFactoryContract;
use LaraPkgs\Validation\Contracts\RulePriorityResolver;

final class RuleFactory implements RuleFactoryContract
{
    protected RulePriorityResolver $rulePriorityResolver;

    public function __construct(RulePriorityResolver $priorityResolver)
    {
        $this->rulePriorityResolver = $priorityResolver;
    }

    /**
     * @param array<array-key, mixed> $arguments
     */
    public function make(string $name, array $arguments = []): ValidationRule
    {
        $priority = $this->rulePriorityResolver->resolve($name);

        return new ValidationRule($name, $arguments, $priority);
    }
}