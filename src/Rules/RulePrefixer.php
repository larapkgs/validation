<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaraPkgs\Validation\Contracts\RulePrefixer as RulePrefixerContract;
use LaraPkgs\Validation\Contracts\ValidationRule;

final class RulePrefixer implements RulePrefixerContract
{
    /**
     * @var array<int, string>
     */
    protected array $prefixableArguments = ['field', 'fields'];

    public function prefix(ValidationRule $rule, string $prefix): ValidationRule
    {
        if(!Arr::hasAny($arguments = $rule->getArguments(), $this->prefixableArguments)) {
            return $rule;
        };

        $arguments = Collection::make($arguments)->map(function (mixed $value, string $argument) use ($prefix) {
            return $this->shouldApplyPrefix($argument)
                ? $this->applyPrefix($value, $prefix)
                : $value;

        })->all();

        return $rule->withArguments($arguments);
    }

    protected function shouldApplyPrefix(string $argument):bool
    {
        return in_array($argument, $this->prefixableArguments);
    }

    /**
     * @param array<array-key, string>|string $value
     * @return array<array-key, string>|string
     */
    protected function applyPrefix(array|string $value, string $prefix): array|string
    {
        return is_array($value)
            ? $this->applyPrefixToArray($value, $prefix)
            : $this->applyPrefixToString($value, $prefix);
    }

    /**
     * @param array<array-key, string> $array
     * @return array<array-key, string>
     */
    protected function applyPrefixToArray(array $array, string $prefix): array
    {
        return Collection::make($array)
            ->map(fn(string $value) => $this->applyPrefixToString($value, $prefix))
            ->all();
    }

    protected function applyPrefixToString(string $value, string $prefix): string
    {
        $prefix = Str::finish($prefix, '.');

        return $prefix . $value;
    }
}