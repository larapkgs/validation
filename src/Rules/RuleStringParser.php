<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Contracts\ValidationRule as ValidationRuleContract;

final class RuleStringParser
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
     */
    public function parse(string $subject): array
    {
        return Str::of($subject)->explode('|')
            ->map(fn(string $ruleString) => $this->parseRuleString($ruleString))
            ->all();
    }

    protected function parseRuleString(string $string): ValidationRuleContract
    {
        $parsed = Str::of($string)->explode(':');
        $name = $parsed->first();
        $arguments = $parsed->count() > 1
            ? $this->parseArgumentsString($parsed->last())
            : [];

        return $this->ruleFactory->make($name, $arguments);
    }

    protected function parseArgumentsString(string $arguments): array
    {
        return Str::of($arguments)->explode(',')
            ->mapWithKeys(function(string $argument, int $idx) {
                return ($parsed = Str::of($argument)->explode('='))->count() > 1
                    ? [$parsed->first() => $argument]
                    : [$idx => $argument];
            })
            ->all();
    }
}