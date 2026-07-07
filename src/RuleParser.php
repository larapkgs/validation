<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaraPkgs\Validation\Exceptions\UnparsableRuleException;

final class RuleParser
{
    /**
     * @return array<string, mixed>
     * @throws UnparsableRuleException
     */
    public function parse($subject): array
    {
        return match(true) {
            is_array($subject) => $this->parseArray($subject),
            is_object($subject) => $this->parseObject($subject),
            is_string($subject) => $this->parseString($subject),
            default => throw new UnparsableRuleException($subject)
        };
    }

    /**
     * @param array<array-key, mixed> $subject
     * @return array<string, mixed>
     */
    protected function parseArray(array $subject): array
    {
        return new Collection($subject)
            ->reduce(function(array $parsed, mixed $parsable) {
                return [...$parsed, ...$this->parse($parsable)];
            }, []);
    }

    /**
     * @return array<string, object>
     */
    protected function parseObject(object $subject): array
    {
        return [$subject::class => $subject];
    }

    /**
     * @return array<string, string>
     */
    protected function parseString(string $subject): array
    {
        return Str::of($subject)->explode('|')
            ->mapWithKeys(fn(string $ruleString) => $this->parseRuleString($ruleString))
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function parseRuleString(string $ruleString): array
    {
        $name = Str::of($ruleString)->explode(':')->first();

        return [$name => $ruleString];
    }
}
