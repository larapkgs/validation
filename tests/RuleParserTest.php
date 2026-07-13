<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Exceptions\UnparsableRuleException;
use LaraPkgs\Validation\RuleParser;
use LaraPkgs\Validation\Rules\RuleStringParser;
use LaraPkgs\Validation\Rules\ValidationRule;

it('expects an instance of the RuleStringParser and the RuleFactory on instantiation', function () {
    $ruleStringParser = RuleStringParser::make();
    $ruleFactory = App::make(RuleFactory::class);
    $parser = new RuleParser($ruleStringParser, $ruleFactory);

   expect($parser)->toBeInstanceOf(RuleParser::class);
});

it('resolves as a singleton from the service container', function () {
    $parser = App::make(RuleParser::class);
    expect($parser)->toBeInstanceOf(RuleParser::class);

    $parser2 = App::make(RuleParser::class);

    expect($parser2)
        ->toBeInstanceOf(RuleParser::class)
        ->toBe($parser);
});

describe('RuleParser::make', function () {
    it('provides a factory method', function () {
        $parser = RuleParser::make();

        expect($parser)->toBeInstanceOf(RuleParser::class);
    });
});

describe('RuleParser::parse', function () {
    beforeEach(function () {
       $this->parser = RuleParser::make();
    });

    it('keeps ValidationRule objects untouched ', function () {
        $subject = new ValidationRule('test', $arguments = ['arg1' => 'value1', 'arg2' => 'value2']);

        $parsed = $this->parser->parse($subject);

        expect($parsed)
            ->toHaveCount(1)
            ->sequence(
                fn($rule) => $rule->toBe($subject)
                    ->getName()->toBe('test')
                    ->getArguments()->toBe($arguments)
            );
    });

    it('parses objects', function () {
        $subject = Rule::string();

        $parsed = $this->parser->parse($subject);

        expect($parsed)
            ->toHaveCount(1)
            ->sequence(
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe($subject::class)
                    ->getArguments()->toBe([$subject])
            );
    });

    it('parses strings', function () {
        $subject = 'required|min:10|between:1,100|dimensions:min_ratio=1/2,max_ratio=3/2';

        $parsed = $this->parser->parse($subject);

        expect($parsed)
            ->toHaveCount(4)
            ->sequence(
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('required')
                    ->getArguments()->toBeEmpty(),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('min')
                    ->getArguments()->toBe(['10']),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('between')
                    ->getArguments()->toBe(['1', '100']),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('dimensions')
                    ->getArguments()->toBe(['min_ratio' => 'min_ratio=1/2', 'max_ratio' => 'max_ratio=3/2'])
            );
    });

    it('parses arrays', function () {
        $validationRuleObject = new ValidationRule('test', $arguments = ['arg1' => 'value1', 'arg2' => 'value2']);
        $laravelRuleObject = Rule::string();
        $subject = [$validationRuleObject, $laravelRuleObject, 'required|min:10','max:100'];

        $parsed = $this->parser->parse($subject);

        expect($parsed)
            ->toHaveCount(5)
            ->sequence(
                fn($rule) => $rule->toBe($validationRuleObject)
                    ->getName()->toBe('test')
                    ->getArguments()->toBe($arguments),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe($laravelRuleObject::class)
                    ->getArguments()->toBe([$laravelRuleObject]),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('required')
                    ->getArguments()->toBeEmpty(),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('min')
                    ->getArguments()->toBe(['10']),
                fn($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe('max')
                    ->getArguments()->toBe(['100'])
            );
    });

    it('rejects unparsable rules', function () {
        $subject = 1;

        expect(fn() => $this->parser->parse($subject))
            ->toThrow(UnparsableRuleException::class);
    })->with([1, true, new StdClass()]);
});