<?php

namespace LaraPkgs\Validation\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\ValidationCollection;

trait IsValidatable
{
    protected ?ValidationCollection $validationCollection = null;

    public function getValidationCollection(): ValidationCollection
    {
        return clone $this->resolveValidationCollection();
    }

    protected function resolveValidationCollection(): ValidationCollection
    {
        return clone $this->validationCollection ??= $this->makeValidationCollection();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function makeValidator(array $data): Validator
    {
        return $this->resolveValidationCollection()->makeValidator($data);
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
     * @param array<string, string> $messages
     * @return array<string, string>
     */
    protected function prefixValidationMessages(array $messages, string $errorBagPrefix): array
    {
        return Collection::make($messages)
            ->mapWithKeys(fn(array $messages, string $key) => [$errorBagPrefix . $key => $messages])
            ->all();
    }

    protected abstract function makeValidationCollection(): ValidationCollection;
}