<?php

declare(strict_types=1);

use Illuminate\Validation\Rule;
use LaraPkgs\Validation\Exceptions\UnparsableRuleException;
use LaraPkgs\Validation\RuleParser;

describe('RuleParser::parse', function () {
    it('parses objects', function () {
        $parser = new RuleParser();
        $subject = Rule::string();

        $parsed = $parser->parse($subject);

        expect($parsed)->toBe([get_class($subject) => $subject]);
    });

    it('parses strings', function () {
        $parser = new RuleParser();
        $subject = 'required|min:10';

        $parsed = $parser->parse($subject);

        expect($parsed)->toBe([
            'required' => 'required',
            'min' => 'min:10',
        ]);
    });

    it('parses arrays', function () {
        $parser = new RuleParser();
        $subject = ['required|min:10', 'max:100', $object = Rule::string()];

        $parsed = $parser->parse($subject);

        expect($parsed)->toBe([
            'required' => 'required',
            'min' => 'min:10',
            'max' => 'max:100',
            get_class($object) => $object
        ]);
    });

    it('overwrites duplicate rules', function () {
        $parser = new RuleParser();
        $subject = ['min:10', 'min:100'];

        $parsed = $parser->parse($subject);

        expect($parsed)->toBe(['min' => 'min:100']);
    });

    it('rejects unparsable rules', function () {
        $parser = new RuleParser();
        $subject = 1;

        expect(fn() => $parser->parse($subject))
            ->toThrow(UnparsableRuleException::class);
    })->with([1, true, new StdClass()]);
});