<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Closure;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Concerns\HasFluentRules;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\RuleFactory;

final class ValidationItem
{
    use HasFluentRules;
    use IsValidatable;

    protected string $key;

    protected RuleFactory $ruleFactory;

    protected RuleCollection $rules;

    protected ValidatorFactory $validatorFactory;

    /**
     * @var array<string, string>
     */
    protected array $messages = [];

    protected ?string $customAttribute = null;

    public static function make(string $key, mixed ...$rules)
    {
        $ruleFactory = App::make(RuleFactory::class);
        $validatorFactory = App::make(ValidatorFactory::class);

        return new self($ruleFactory, $validatorFactory, $key, ...$rules);
    }

    public function __construct(RuleFactory $ruleFactory, ValidatorFactory $validatorFactory, string $key, mixed ...$rules)
    {
        $this->ruleFactory = $ruleFactory;
        $this->validatorFactory = $validatorFactory;
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
     * @param array<string, mixed> $data
     */
    public function makeValidator(array $data): Validator
    {
        return $this->validatorFactory->make($data, ...$this->toValidatorArguments());
    }

    /**
     * @return array{rules: array<int, mixed>, messages: array<string, string>, attributes: string}
     */
    public function toValidatorArguments(): array
    {
        return [
            'rules' => $this->prepareRulesForValidator(),
            'messages' => $this->prepareMessagesForValidator(),
            'attributes' => $this->prepareAttributeForValidator()
        ];
    }

    protected function prepareRulesForValidator(): array
    {
        return [$this->key => $this->rules->toArray()];
    }

    protected function prepareMessagesForValidator(): array
    {
        return Collection::make($this->messages)
            ->mapWithKeys(fn(string $message, string $rule) => [$this->key . '.' . $rule => $message])
            ->all();
    }

    protected function prepareAttributeForValidator(): array
    {
        return [$this->key => $this->getCustomAttribute()];
    }
}
