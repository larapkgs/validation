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

    public function resolve(string $type): int
    {
        $type = $this->ruleTypeResolver->resolve($type);

        return Config::get('validation.type_to_priority_map.' . $type)
            ?? Config::get('validation.default_rule_priority', 100);
    }
}
