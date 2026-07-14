<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaraPkgs\Validation\Contracts\ValidationRule as ValidationRuleContract;

final class ValidationRule implements ValidationRuleContract
{
    protected string $name;

    /** @var array<array-key, mixed> */
    protected array $arguments;

    protected int $priority;

    /**
     * @param array<array-key, mixed> $arguments
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
     * @param  array<array-key, mixed> $arguments
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

    public function asValidatorRule(): string|object
    {
        $arguments = Collection::make($this->arguments);

        if($arguments->count() == 0) {
            return $this->getName();
        }

        if($arguments->count() === 1 && is_object($arguments->first())) {
            return $arguments->first();
        }

        $arguments = $arguments->reduce(function (Collection $arguments, mixed $argument) {
            return is_array($argument) ? $arguments->merge($argument) : $arguments->push($argument);
        }, Collection::make());

        return Str::of($this->getName())
            ->append(':')
            ->append($arguments->join(','))
            ->toString();
    }
}