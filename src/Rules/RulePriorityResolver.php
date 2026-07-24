<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Illuminate\Support\Facades\Config;
use LaraPkgs\Validation\Contracts\RulePriorityResolver as RulePriorityResolverContract;
use LaraPkgs\Validation\Contracts\RuleTypeResolver;

final class RulePriorityResolver implements RulePriorityResolverContract
{
    protected RuleTypeResolver $ruleTypeResolver;

    public function __construct(RuleTypeResolver $ruleTypeResolver)
    {
        $this->ruleTypeResolver = $ruleTypeResolver;
    }

    public function resolve(string $rule): int
    {
        $type = $this->ruleTypeResolver->resolve($rule);

        return Config::get('validation.typeToPriorityMap.' . $type, 100);
    }
}
