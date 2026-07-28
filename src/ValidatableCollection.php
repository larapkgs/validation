<?php

declare(strict_types=1);

namespace LaraPkgs\Validation;

use Countable;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryContract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaraPkgs\Validation\Concerns\IsValidatable;
use LaraPkgs\Validation\Contracts\ProvidesValidatableCollection;
use LaraPkgs\Validation\Contracts\Validatable;

final class ValidatableCollection implements Countable, Validatable
{
    use IsValidatable;

    /** @var Collection<string, ValidatableBuilder> */
    protected Collection $items;

    protected ValidationFactoryContract $validationFactory;

    public static function make(ValidatableBuilder ...$items): self
    {
        $factory = App::make(ValidationFactoryContract::class);

        return new self($factory, ...$items);
    }

    public function __construct(ValidationFactoryContract $validationFactory, ValidatableBuilder ...$items)
    {
        $this->items = new Collection;
        $this->validationFactory = $validationFactory;

        $this->processItems(...$items);
    }

    public function __clone(): void
    {
        $this->items = $this->cloneItems();
    }

    /**
     * @return Collection<string, ValidatableBuilder>
     */
    public function getItems(): Collection
    {
        return $this->cloneItems();
    }

    /**
     * @return Collection<string, ValidatableBuilder>
     */
    protected function cloneItems(): Collection
    {
        return $this->items->map(fn (ValidatableBuilder $item) => clone $item);
    }

    protected function processItems(ValidatableBuilder ...$items): self
    {
        foreach ($items as $item) {
            $this->items->put($item->getKey(), clone $item);
        }

        return $this;
    }

    public function add(ValidatableBuilder ...$items): self
    {
        return (clone $this)->processItems(...$items);
    }

    public function prefix(string $prefix): self
    {
        $items = $this->items
            ->map(fn (ValidatableBuilder $item) => $item->prefix($prefix))
            ->values()->all();

        return self::make(...$items);
    }

    public function merge(ValidatableCollection|ProvidesValidatableCollection ...$mergeables): self
    {
        /** @var array<int, ValidatableBuilder> $items */
        $items = new Collection([$this, ...$mergeables])
            ->map(function (ValidatableCollection|ProvidesValidatableCollection $mergeable) {
                return $mergeable instanceof ProvidesValidatableCollection
                    ? $mergeable->getValidatableCollection()
                    : $mergeable;
            })
            ->reduce(function (array $carry, ValidatableCollection $collection) {
                return array_merge($carry, $collection->getItems()->all());
            }, []);

        return self::make(...$items);
    }

    /**
     * @param  array<string, mixed>  $data
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
        $arguments = $this->items->reduce(function (array $carry, ValidatableBuilder $item) {
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
