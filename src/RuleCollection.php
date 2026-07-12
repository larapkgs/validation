<?php

namespace LaraPkgs\Validation;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Concerns\HasFluentRules;
use LaraPkgs\Validation\Contracts\ValidationRule;
use LaraPkgs\Validation\Rules\RuleFactory;

final class RuleCollection implements Arrayable, Countable
{
    use HasFluentRules;

    protected RuleFactory $factory;

    protected RuleParser $parser;

    /**
     * @var Collection<string, ValidationRule>
     */
    protected Collection $rules;

    public static function make(mixed ...$rules): self
    {
        $factory = App::make(RuleFactory::class);
        $parser = App::make(RuleParser::class);

        return new self($factory, $parser, ...$rules);
    }

    public function __construct(RuleFactory $factory, RuleParser $parser, mixed ...$rules)
    {
        $this->parser = $parser;
        $this->factory = $factory;
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

    protected function applyFluentRule(string $ruleName, array $arguments = []): self
    {
        $rule = $this->factory->make($ruleName, $arguments);

        return $this->add($rule);
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
    public function toArray(): array
    {
        return $this->rules
            ->sortBy(fn(ValidationRule $rule) => $rule->getPriority())
            ->map(fn(ValidationRule $rule) => $rule->asValidatorRule())
            ->values()->all();
    }

    public function count(): int
    {
        return $this->rules->count();
    }
}
