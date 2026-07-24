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
use LaraPkgs\Validation\Contracts\Validatable;
use LaraPkgs\Validation\Contracts\ValidationRule;
use LaraPkgs\Validation\Rules\RuleCollection;

final class ValidatableBuilder implements Validatable
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

    public static function make(string $key): self
    {
        $ruleFactory = App::make(RuleFactory::class);
        $validatorFactory = App::make(ValidatorFactory::class);

        return new self($ruleFactory, $validatorFactory, $key);
    }

    public function __construct(RuleFactory $ruleFactory, ValidatorFactory $validatorFactory, string $key)
    {
        $this->ruleFactory = $ruleFactory;
        $this->validatorFactory = $validatorFactory;
        $this->key = $key;
        $this->rules = RuleCollection::make();
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
        return $this->newInstance(function (self $instance) use ($prefix) {
            $instance->key = $prefix . $instance->key;
        });
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function applyFluentRule(string|ValidationRule $rule, array $arguments = []): self
    {
        return $this->newInstance(function (self $instance) use ($rule, $arguments) {
            $rule = is_string($rule)
                ? $this->ruleFactory->make($rule, $arguments)
                : clone $rule;

            $instance->rules = $instance->rules->applyRule($rule);
        });
    }

    public function getRules(): RuleCollection
    {
        return clone $this->rules;
    }

    /**
     * @param  array<string, string>  $messages
     */
    public function addMessages(array $messages): self
    {
        return $this->newInstance(function (self $instance) use ($messages) {
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
        return $this->newInstance(function (self $instance) use ($customAttribute) {
            $instance->customAttribute = $customAttribute;
        });
    }

    public function getCustomAttribute(): string
    {
        return $this->customAttribute ?? $this->key;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function makeValidator(array $data): Validator
    {
        return $this->validatorFactory->make($data, ...$this->toValidatorArguments());
    }

    /**
     * @return array{rules: array<string, array<int, mixed>>, messages: array<string, string>, attributes: array<string, string>}
     */
    public function toValidatorArguments(): array
    {
        return [
            'rules' => $this->prepareRulesForValidator(),
            'messages' => $this->prepareMessagesForValidator(),
            'attributes' => $this->prepareAttributeForValidator(),
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function prepareRulesForValidator(): array
    {
        return [$this->key => $this->rules->toValidatorArgument()];
    }

    /**
     * @return array<string, string>
     */
    protected function prepareMessagesForValidator(): array
    {
        return Collection::make($this->messages)
            ->mapWithKeys(fn (string $message, string $rule) => [$this->key . '.' . $rule => $message])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function prepareAttributeForValidator(): array
    {
        return [$this->key => $this->getCustomAttribute()];
    }
}
