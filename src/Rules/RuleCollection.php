<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Countable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Concerns\HasFluentRules;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\Contracts\RulePrefixer;
use LaraPkgs\Validation\Contracts\ValidationRule;

final class RuleCollection implements Countable
{
    use HasFluentRules;

    protected RuleFactory $factory;

    protected RuleParser $parser;

    protected RulePrefixer $prefixer;

    /**
     * @var Collection<string, ValidationRule>
     */
    protected Collection $rules;

    public static function make(mixed ...$rules): self
    {
        $factory = App::make(RuleFactory::class);
        $parser = App::make(RuleParser::class);
        $prefixer = App::make(RulePrefixer::class);

        return new self($factory, $parser, $prefixer, ...$rules);
    }

    public function __construct(RuleFactory $factory, RuleParser $parser, RulePrefixer $prefixer, mixed ...$rules)
    {
        $this->parser = $parser;
        $this->factory = $factory;
        $this->prefixer = $prefixer;
        $this->rules = Collection::make();

        $this->processRules(...$rules);
    }

    protected function processRules(mixed...$rules): self
    {
        foreach ($this->parser->parse($rules) as $rule) {
            $this->rules->put($rule->getName(), $rule);
        }

        return $this;
    }

    public function add(mixed ...$rules): self
    {
        return $this->processRules(...$rules);
    }

    /**
     * @param array<string, mixed> $arguments
     */
    protected function applyFluentRule(string|ValidationRule $rule, array $arguments = []): self
    {
        if(is_string($rule)) {
            $rule = $this->factory->make($rule, $arguments);
        }

        return $this->add($rule);
    }

    public function prefix(string $prefix): self
    {
        $this->rules = $this->rules
            ->map(function(ValidationRule $rule) use ($prefix) {
                return $this->prefixer->prefix($rule, $prefix);
            });

        return $this;
    }

    public function has(string $name): bool
    {
        return $this->rules->has($name);
    }

    public function forget(string $name): self
    {
        $this->rules->forget($name);

        return $this;
    }

    public function isEmpty(): bool
    {
        return $this->rules->isEmpty();
    }

    /**
     * @return array<int, mixed>
     */
    public function toValidatorArgument(): array
    {
        return $this->rules
            ->sortBy(fn(ValidationRule $rule) => $rule->getPriority())
            ->map(fn(ValidationRule $rule) => $rule->toValidatorRule())
            ->values()->all();
    }

    public function count(): int
    {
        return $this->rules->count();
    }
}
