<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Countable;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryContract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Concerns\IsValidatable;

final class ValidationCollection implements Countable
{
    use IsValidatable;

    /** @var Collection<string, ValidationItem> */
    protected Collection $items;

    protected ValidationFactoryContract $validationFactory;

    public static function make(ValidationItem ...$items): self
    {
        $factory = App::make(ValidationFactoryContract::class);

        return new self($factory, ...$items);
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

    /**
     * @return Collection<string, ValidationItem>
     */
    public function getItems(): Collection
    {
        return $this->cloneItems();
    }

    /**
     * @return Collection<string, ValidationItem>
     */
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

        return self::make(...$items);
    }

    public function merge(ValidationCollection ...$validationCollections): self
    {
        /** @var array<int, ValidationItem> $items */
        $items = new Collection([$this, ...$validationCollections])
            ->reduce(function(array $carry, ValidationCollection $collection) {
                return array_merge($carry, $collection->getItems()->all());
            }, []);

        return self::make(...$items);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function makeValidator(array $data): Validator
    {
        return $this->validationFactory->make($data, ...$this->toValidatorArguments());
    }

    /**
     * @return array{rules: array<string, array<int, string>>, messages: array<string, string>, attributes: array<string, string>}
     */
    public function toValidatorArguments(): array
    {
        /** @var array{rules: array<string, array<int, string>>, messages: array<string, string>, attributes: array<string, string>} $arguments */
        $arguments = $this->items->reduce(function (array $carry, ValidationItem $item) {
            $itemArguments = $item->toValidatorArguments();

            $carry['rules'] = array_merge($carry['rules'], $itemArguments['rules']);
            $carry['messages'] = array_merge($carry['messages'], $itemArguments['messages']);
            $carry['attributes'] = array_merge($carry['attributes'], $itemArguments['attributes']);

            return $carry;
        }, ['rules' => [], 'messages' => [], 'attributes' => []]);

        return $arguments;
    }

    public function count(): int
    {
        return $this->items->count();
    }
}
