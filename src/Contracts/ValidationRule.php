<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Contracts;

interface ValidationRule
{
    public function getName(): string;

    /**
     * @param  array<array-key, mixed>  $arguments
     */
    public function withArguments(array $arguments): self;

    /**
     * @return array<array-key, mixed>
     */
    public function getArguments(): array;

    public function getPriority(): int;

    public function toValidatorRule(): string|object;
}
