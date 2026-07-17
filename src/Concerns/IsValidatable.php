<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\ValidatableCollection;

trait IsValidatable
{
    /**
     * @param array<string, mixed> $data
     */
    public function passes(array $data): bool
    {
        return !$this->fails($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function fails(array $data): bool
    {
        return $this->makeValidator($data)->fails();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function validate(array $data, ?string $errorBagPrefix = null): array
    {
        try {
            return $this->makeValidator($data)->validate();
        } catch(ValidationException $exception) {
            return $this->handleValidationExceptions($exception, $errorBagPrefix);
        }
    }

    protected function handleValidationExceptions(ValidationException $exception, ?string $errorBagPrefix = null): mixed
    {
        if($errorBagPrefix !== null) {
            $messages = $this->prefixValidationMessages($exception->errors(), $errorBagPrefix);

            $exception = ValidationException::withMessages($messages);
        }

        throw $exception;
    }

    /**
     * @param array<string, array<int, string>> $messages
     * @return array<string, array<int, string>>
     */
    protected function prefixValidationMessages(array $messages, string $errorBagPrefix): array
    {
        return Collection::make($messages)
            ->mapWithKeys(fn(array $messages, string $key) => [$errorBagPrefix . $key => $messages])
            ->all();
    }

    abstract public function makeValidator(array $data): Validator;
}