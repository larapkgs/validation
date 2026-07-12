<?php

namespace LaraPkgs\Validation\Contracts;

interface ValidationRule
{
    public function getName(): string;

    /**
     * @return array<array-key, mixed>
     */
    public function getArguments(): array;

    public function getPriority(): int;

    public function asValidatorRule(): string|object;
}