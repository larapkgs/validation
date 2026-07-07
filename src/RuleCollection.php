<?php

namespace LaraPkgs\Validation;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

final class RuleCollection implements Arrayable, Countable
{
    protected RuleParser $parser;

    /**
     * @var array<string, mixed>
     */
    protected array $rules = [];

    public function __construct(mixed ...$rules)
    {
        $this->add(...$rules);
    }

    public function add(...$rules): self
    {
        foreach($rules as $rule) {
            $parsed = $this->parse($rule);

            $this->rules = [...$this->rules, ...$parsed];
        }

        return $this;
    }

    public function has(string $name): bool
    {
        return Arr::has($this->rules, $name);
    }

    public function forget(string $name): self
    {
        Arr::forget($this->rules, $name);

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->rules);
    }

    // Parsing
    protected function getParser(): RuleParser
    {
        return new RuleParser();
    }

    protected function parse(mixed $rule): array
    {
        return collect($this->getParser()->parse($rule))
            ->each((fn(string $rule, string $name) => $this->fireParsedHook($rule, $name)))
            ->all();
    }

    protected function fireParsedHook($rule, $name): void
    {
        match($name) {
            'required' => $this->parsedRequired(),
            'nullable' => $this->parsedNullable(),
            default => null
        };
    }

    protected function parsedRequired(): void
    {
        if($this->has('nullable')) $this->forget('nullable');
    }

    protected function parsedNullable(): void
    {
        if($this->has('required')) $this->forget('required');
    }

    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        $rules = $this->rules;

        foreach(['required', 'nullable'] as $name) {
            if(!Arr::has($rules, $name)) continue;

            Arr::forget($rules, $name);
            $rules = Arr::prepend($rules, $name , $name);
        }

        return array_values($rules);
    }

    public function count(): int
    {
        return count($this->rules);
    }
}
