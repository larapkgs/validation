<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Concerns\HasFluentRules;
use LaraPkgs\Validation\Contracts\RuleFactory;

final class ValidationItem implements Arrayable
{
    use HasFluentRules;

    protected string $key;

    protected RuleFactory $ruleFactory;

    protected RuleCollection $rules;

    /**
     * @var array<string, string>
     */
    protected array $messages = [];

    protected ?string $customAttribute = null;

    public static function make(string $key, mixed ...$rules)
    {
        $ruleFactory = App::make(RuleFactory::class);

        return new self($ruleFactory, $key, ...$rules);
    }

    public function __construct(RuleFactory $ruleFactory, string $key, mixed ...$rules)
    {
        $this->ruleFactory = $ruleFactory;
        $this->key = $key;
        $this->rules = RuleCollection::make(...$rules);
    }

    public function __clone()
    {
        $this->rules = clone $this->rules;
    }

    protected function newInstance(Closure $callback): self
    {
        $instance = clone $this;

        return tap($instance, $callback);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function prefix(string $prefix): self
    {
        return $this->newInstance(function(self $instance) use ($prefix) {
            $instance->key = $prefix . $instance->key;
        });
    }

    public function addRules(mixed ...$rules): self
    {
        return $this->newInstance(function(self $instance) use ($rules) {
            $instance->rules->add(...$rules);
        });
    }

    public function applyFluentRule(string $ruleName, array $arguments = []): self
    {
        return $this->newInstance(function(self $instance) use ($ruleName, $arguments) {
            $rule = $this->ruleFactory->make($ruleName, $arguments);

            $instance->rules->add($rule);
        });
    }

    /**
     * @return array<int, mixed>
     */
    public function getRules(): array
    {
        return $this->rules->toArray();
    }

    public function addMessages(array $messages): self
    {
        return $this->newInstance(function(self $instance) use ($messages) {
            $instance->messages = array_merge($instance->messages, $messages);
        });
    }

    /**
     * @return array<string, string>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setCustomAttribute(string $customAttribute): self
    {
        return $this->newInstance(function(self $instance) use ($customAttribute) {
            $instance->customAttribute = $customAttribute;
        });
    }

    public function getCustomAttribute(): string
    {
        return $this->customAttribute ?? $this->key;
    }

    /**
     * @return array{rules: array<int, mixed>, messages: array<string, string>, attribute: string}
     */
    public function toArray(): array
    {
        return [
            'rules' => $this->getRules(),
            'messages' => $this->getMessages(),
            'attribute' => $this->getCustomAttribute()
        ];
    }
}
