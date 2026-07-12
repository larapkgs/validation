<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Rules;

use Illuminate\Support\Collection;
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

    public function getName(): string
    {
        return $this->name;
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

        return match (true) {
            $arguments->count() === 0 => $this->getName(),
            $arguments->count() === 1 && is_object($arguments->first()) => $arguments->first(),
            default => $this->getName() . ':' . $arguments->join(',')
        };
    }
}