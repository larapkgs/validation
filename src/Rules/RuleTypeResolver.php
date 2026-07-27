<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use LaraPkgs\Validation\Contracts\RuleTypeResolver as RuleTypeResolverContract;

final class RuleTypeResolver implements RuleTypeResolverContract
{
    /**
     * @var Collection<string, string>|null
     */
    protected ?Collection $ruleToTypeMap = null;

    public function resolve(string $ruleName): string
    {
        return $this->getRuleToTypeMap()->get($ruleName) ?? Config::get('validation.default_rule_type', 'constraint');
    }

    /**
     * @return Collection<string, string>
     */
    protected function getRuleToTypeMap(): Collection
    {
        return $this->ruleToTypeMap ??= $this->resolveRuleToTypeMap();
    }

    /**
     * @return Collection<string, string>
     */
    protected function resolveRuleToTypeMap(): Collection
    {
        return Collection::make($this->getTypeToRuleMapFromConfig())
            ->flatMap(function (array $rules, string $type) {
                return Collection::make($rules)->mapWithKeys(fn (string $rule) => [$rule => $type]);
            });
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function getTypeToRuleMapFromConfig(): array
    {
        return Config::get('validation.type_to_rule_map', []);
    }
}
