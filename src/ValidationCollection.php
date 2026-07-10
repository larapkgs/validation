<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryContract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;

final class ValidationCollection implements Arrayable, Countable
{
    protected Collection $items;

    protected ValidationFactoryContract $validationFactory;

    public static function make(ValidationItem ...$items): static
    {
        return new static(app(ValidationFactoryContract::class), ...$items);
    }

    public function __construct(ValidationFactoryContract $validationFactory, ValidationItem ...$items)
    {
        $this->items = new Collection();
        $this->validationFactory = $validationFactory;

        $this->processItems(...$items);
    }

    public function __clone(): void
    {
        $this->items = $this->cloneItems();
    }

    public function getItems(): Collection
    {
        return $this->cloneItems();
    }

    protected function cloneItems(): Collection
    {
        return $this->items->map(fn(ValidationItem $item) => clone $item);
    }

    protected function processItems(ValidationItem ...$validationItems): self
    {
        foreach ($validationItems as $validationItem) {
            $this->items->put($validationItem->getKey(), $validationItem);
        }

        return $this;
    }

    public function add(ValidationItem ...$validationItems): self
    {
        return (clone $this)->processItems(...$validationItems);
    }

    public function prefix(string $prefix): self
    {
        $items = $this->items
            ->map(fn(ValidationItem $item) => $item->prefix($prefix))
            ->values()->all();

        return static::make(...$items);
    }

    public function makeValidator(array $data): Validator
    {
        return $this->validationFactory->make($data, ...$this->toArray());
    }

    // Arrayable implementation
    public function toArray(): array
    {
        return $this->items->reduce(function(array $carry, ValidationItem $item, string $key) {
            $carry['rules'][$key] = $item->getRules();
            $carry['attributes'][$key] = $item->getCustomAttribute();

            return $this->mergeMessagesIntoCarry($carry, $key, $item->getMessages());
        }, ['rules' => [], 'messages' => [], 'attributes' => []]);
    }

    protected function mergeMessagesIntoCarry(array $carry, string $key, array $messages): array
    {
        $prepared = Collection::make($messages)
            ->mapWithKeys(fn(string $message, string $rule) => [$key . '.' . $rule => $message])
            ->all();

        $carry['messages'] = array_merge($carry['messages'], $prepared);

        return $carry;
    }

    public function count(): int
    {
        return $this->items->count();
    }
}
