<?php

use LaraPkgs\Validation\Tests\TestSupport\HasFluentRuleTestClass;
use LaraPkgs\Validation\Tests\TestSupport\FluentRuleDataset;

dataset('fluent rules', fn() => new FluentRuleDataset()->make());

beforeEach(function () {
    $this->subject = new HasFluentRuleTestClass();
});

it('forwards all fluent method calls to the abstract applyFluentRule method', function (
    string $method,
    array $methodArguments,
    string $rule,
    array $ruleArguments
) {
    expect($this->subject->{$method}(...$methodArguments))
        ->toBe($this->subject)
        ->getRules()->toBe([$rule => $ruleArguments]);
})->with('fluent rules');
