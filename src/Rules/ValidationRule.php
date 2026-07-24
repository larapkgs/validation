<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Closure;
use Illuminate\Support\Collection;
use LaraPkgs\Validation\Contracts\ValidationRule as ValidationRuleContract;

final class ValidationRule implements ValidationRuleContract
{
    protected string $name;

    /** @var array<array-key, mixed> */
    protected array $arguments;

    protected int $priority;

    /** @var (Closure(self): (string|object))|null */
    protected ?Closure $validatorRuleResolver = null;

    /**
     * @param  array<array-key, mixed>  $arguments
     */
    public function __construct(string $name, array $arguments = [], int $priority = 100)
    {
        $this->name = $name;
        $this->arguments = $arguments;
        $this->priority = $priority;
    }

    public function __clone()
    {
        $this->arguments = new Collection($this->arguments)
            ->map(fn ($argument) => is_object($argument) ? clone $argument : $argument)
            ->all();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  array<array-key, mixed>  $arguments
     */
    public function withArguments(array $arguments): self
    {
        $clone = clone $this;
        $clone->arguments = array_merge($clone->arguments, $arguments);

        return $clone;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function toValidatorRuleUsing(callable $validatorRuleResolver): self
    {
        if (! $validatorRuleResolver instanceof Closure) {
            $validatorRuleResolver = $validatorRuleResolver(...);
        }

        $clone = clone $this;
        $clone->validatorRuleResolver = $validatorRuleResolver;

        return $clone;
    }

    public function toValidatorRule(): string|object
    {
        if ($this->validatorRuleResolver !== null) {
            return ($this->validatorRuleResolver)($this);
        }

        $arguments = Collection::make($this->arguments);

        if ($arguments->count() == 0) {
            return $this->getName();
        }

        if ($arguments->count() === 1 && is_object($arguments->first())) {
            return clone $arguments->first();
        }

        $arguments = $arguments
            ->flatMap(fn (mixed $argument) => is_array($argument) ? $argument : [$argument])
            ->map(fn (mixed $argument) => $this->formatArgument($argument))
            ->join(',');

        return $this->getName() . ':' . $arguments;
    }

    protected function formatArgument(mixed $argument): mixed
    {
        return match (true) {
            is_bool($argument) => $argument ? 'true' : 'false',
            default => $argument,
        };
    }
}
