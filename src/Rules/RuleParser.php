<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Contracts\ValidationRule as ValidationRuleContract;
use LaraPkgs\Validation\Exceptions\UnparsableRuleException;

final class RuleParser
{
    protected RuleFactory $ruleFactory;

    public static function make(): self
    {
        return App::make(self::class);
    }

    public function __construct(RuleFactory $ruleFactory)
    {
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @return array<int, ValidationRuleContract>
     * @throws UnparsableRuleException
     */
    public function parse(mixed $subject): array
    {
        return match(true) {
            $subject instanceof ValidationRuleContract => [$subject],
            is_array($subject) => $this->parseArray($subject),
            is_object($subject) => $this->parseObject($subject),
            default => throw new UnparsableRuleException($subject)
        };
    }

    /**
     * @param array<array-key, mixed> $subject
     * @return array<int, ValidationRuleContract>
     */
    protected function parseArray(array $subject): array
    {
        return new Collection($subject)
            ->reduce(function(array $parsed, mixed $parsable) {
                return [...$parsed, ...$this->parse($parsable)];
            }, []);
    }

    /**
     * @return array<int, ValidationRuleContract>
     */
    protected function parseObject(object $subject): array
    {
        $rule = $this->ruleFactory->make($subject::class, [$subject]);

        return [$rule];
    }
}