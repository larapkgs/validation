<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Illuminate\Contracts\Support\Arrayable;

final class ValidationItem implements Arrayable
{
    protected string $key;

    protected RuleCollection $rules;

    /**
     * @var array<string, string>
     */
    protected array $messages = [];

    protected ?string $customAttribute = null;

    public function __construct(string $key, mixed ...$rules)
    {
        $this->key = $key;

        $this->rules = new RuleCollection(...$rules);
    }

    public function __clone()
    {
        $this->rules = clone $this->rules;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function prefix(string $prefix): self
    {
        $cloned = clone $this;
        $cloned->key = $prefix . $cloned->key;

        return $cloned;
    }

    public function addRules(mixed ...$rules): self
    {
        $this->rules->add(...$rules);

        return $this;
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
        $this->messages = array_merge($this->messages, $messages);

        return $this;
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
        $this->customAttribute = $customAttribute;

        return $this;
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
