<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Closure;
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

    public static function make(): self
    {
        $factory = App::make(RuleFactory::class);
        $parser = App::make(RuleParser::class);
        $prefixer = App::make(RulePrefixer::class);

        return new self($factory, $parser, $prefixer);
    }

    public function __construct(RuleFactory $factory, RuleParser $parser, RulePrefixer $prefixer)
    {
        $this->parser = $parser;
        $this->factory = $factory;
        $this->prefixer = $prefixer;
        $this->rules = Collection::make();
    }

    public function __clone()
    {
        $this->rules = $this->rules->map(fn (ValidationRule $rule) => clone $rule);
    }

    protected function newInstance(Closure $callback): self
    {
        $instance = clone $this;

        return tap($instance, $callback);
    }

    /**
     * @param array<string, mixed> $arguments
     */
    protected function applyFluentRule(string|ValidationRule $rule, array $arguments = []): self
    {
        return $this->newInstance(function(self $instance) use ($rule, $arguments) {
            $rule = is_string($rule)
                ? $this->factory->make($rule, $arguments)
                : clone $rule;

            $instance->rules->put($rule->getName(), $rule);
        });
    }

    public function prefix(string $prefix): self
    {
        return $this->newInstance(function(self $instance) use ($prefix) {
            $instance->rules = $instance->rules->map(function(ValidationRule $rule) use ($prefix) {
                return $this->prefixer->prefix($rule, $prefix);
            });
        });
    }

    public function has(string $name): bool
    {
        return $this->rules->has($name);
    }

    public function forget(string $name): self
    {
        return $this->newInstance(function(self $instance) use ($name) {
            $instance->rules->forget($name);
        });
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
