<?php

declare(strict_types=1);

use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Rules\RuleStringParser;
use LaraPkgs\Validation\Rules\ValidationRule;

it('expects an instance of the RuleFactory class', function () {
    $factory = App::make(RuleFactory::class);
    $parser = new RuleStringParser($factory);

    expect($parser)->toBeInstanceOf(RuleStringParser::class);
});

it('resolves as a singleton from the service container', function () {
    $parser = App::make(RuleStringParser::class);
    expect($parser)->toBeInstanceOf(RuleStringParser::class);

    $parser2 = App::make(RuleStringParser::class);

    expect($parser2)
        ->toBeInstanceOf(RuleStringParser::class)
        ->toBe($parser);
});

describe('RuleStringParser::make', function () {
    it('has a factory method', function () {
        $parser = RuleStringParser::make();

        expect($parser)->toBeInstanceOf(RuleStringParser::class);
    });
});

describe('RuleStringParser::parse', function () {
    function parse(string $string) {
        return RuleStringParser::make()->parse($string);
    };

    it('parses strings that contain a single rule without arguments', function () {
        $parsed = parse('required');

        expect($parsed)->toHaveCount(1)
            ->sequence(
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('required')
                    ->getArguments()->toBeEmpty()
                    ->toValidatorRule()->toBe('required')
            );
    });

    it('parses strings that contain a single rule with a single argument', function () {
        $parsed = parse('min:10');

        expect($parsed)->toHaveCount(1)
            ->sequence(
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('min')
                    ->getArguments()->toBe(['10'])
                    ->toValidatorRule()->toBe('min:10')
            );
    });

    it('parses strings that contain a single rule with multiple arguments', function () {
        $parsed = parse('between:1,100');

        expect($parsed)->toHaveCount(1)
            ->sequence(
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('between')
                    ->getArguments()->toBe(['1', '100'])
                    ->toValidatorRule()->toBe('between:1,100')
            );
    });

    it('parses strings that contain a single rule with named arguments', function () {
        $parsed = parse('dimensions:min_ratio=1/2,max_ratio=3/2');

        expect($parsed)->toHaveCount(1)
            ->sequence(
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('dimensions')
                    ->getArguments()->toBe(['min_ratio' => 'min_ratio=1/2', 'max_ratio' => 'max_ratio=3/2'])
                    ->toValidatorRule()->toBe('dimensions:min_ratio=1/2,max_ratio=3/2')
            );
    });

    it('parses strings that contain multiple rules', function () {
        $parsed = parse('required|min:10|between:1,100|dimensions:min_ratio=1/2,max_ratio=3/2');

        expect($parsed)->toHaveCount(4)
            ->sequence(
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('required')
                    ->getArguments()->toBeEmpty()
                    ->toValidatorRule()->toBe('required'),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('min')
                    ->getArguments()->toBe(['10'])
                    ->toValidatorRule()->toBe('min:10'),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('between')
                    ->getArguments()->toBe(['1', '100'])
                    ->toValidatorRule()->toBe('between:1,100'),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('dimensions')
                    ->getArguments()->toBe(['min_ratio' => 'min_ratio=1/2', 'max_ratio' => 'max_ratio=3/2'])
                    ->toValidatorRule()->toBe('dimensions:min_ratio=1/2,max_ratio=3/2')
            );
    });
});