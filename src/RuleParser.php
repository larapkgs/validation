<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use LaraPkgs\Validation\Contracts\ValidationRule as ValidationRuleContract;
use LaraPkgs\Validation\Exceptions\UnparsableRuleException;
use LaraPkgs\Validation\Rules\RuleFactory;
use LaraPkgs\Validation\Rules\RuleStringParser;

final class RuleParser
{
    protected RuleStringParser $ruleStringParser;

    protected RuleFactory $ruleFactory;

    public static function make(): self
    {
        return App::make(self::class);
    }

    public function __construct(RuleStringParser $ruleStringParser, RuleFactory $ruleFactory)
    {
        $this->ruleStringParser = $ruleStringParser;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * @return array<int, ValidationRuleContract>
     * @throws UnparsableRuleException
     */
    public function parse($subject): array
    {
        return match(true) {
            $subject instanceof ValidationRuleContract => [$subject],
            is_array($subject) => $this->parseArray($subject),
            is_object($subject) => $this->parseObject($subject),
            is_string($subject) => $this->parseString($subject),
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

    /**
     * @return array<int, ValidationRuleContract>
     */
    protected function parseString(string $subject): array
    {
        return $this->ruleStringParser->parse($subject);
    }
}