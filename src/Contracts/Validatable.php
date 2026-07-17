<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Contracts;

use Illuminate\Contracts\Validation\Validator;
use LaraPkgs\Validation\ValidationCollection;

interface Validatable
{
    /**
     * @param array<string, mixed> $data
     */
    public function passes(array $data): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function fails(array $data): bool;

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function validate(array $data, ?string $errorBagPrefix = null): array;

    /**
     * @param array<string, mixed> $data
     */
    public function makeValidator(array $data): Validator;
}