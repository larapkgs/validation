<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Exceptions\UnparsableRuleException;
use LaraPkgs\Validation\Rules\RuleParser;
use LaraPkgs\Validation\Rules\ValidationRule;

it('expects an instance of the RuleFactory on instantiation', function () {
    $ruleFactory = App::make(RuleFactory::class);
    $parser = new RuleParser($ruleFactory);

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

describe('RuleParser::make()', function () {
    it('provides a factory method', function () {
        $parser = RuleParser::make();

        expect($parser)->toBeInstanceOf(RuleParser::class);
    });
});

describe('RuleParser::parse()', function () {
    beforeEach(function () {
        $this->parser = RuleParser::make();
    });

    it('keeps ValidationRule objects untouched ', function () {
        $subject = new ValidationRule('test', $arguments = ['arg1' => 'value1', 'arg2' => 'value2']);

        $parsed = $this->parser->parse($subject);

        expect($parsed)
            ->toHaveCount(1)
            ->sequence(
                fn ($rule) => $rule->toBe($subject)
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
                fn ($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe($subject::class)
                    ->getArguments()->toBe([$subject])
            );
    });

    it('parses arrays', function () {
        $validationRuleObject = new ValidationRule('test', $arguments = ['arg1' => 'value1', 'arg2' => 'value2']);
        $laravelRuleObject = Rule::string();
        $subject = [$validationRuleObject, $laravelRuleObject];

        $parsed = $this->parser->parse($subject);

        expect($parsed)
            ->toHaveCount(2)
            ->sequence(
                fn ($rule) => $rule->toBe($validationRuleObject)
                    ->getName()->toBe('test')
                    ->getArguments()->toBe($arguments),
                fn ($rule) => $rule->toBeInstanceOf(ValidationRule::class)
                    ->getName()->toBe($laravelRuleObject::class)
                    ->getArguments()->toBe([$laravelRuleObject])
            );
    });

    it('rejects unparsable rules', function () {
        $subject = 1;

        expect(fn () => $this->parser->parse($subject))
            ->toThrow(UnparsableRuleException::class);
    })->with([1, true, new StdClass]);
});
