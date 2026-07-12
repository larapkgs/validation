<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaraPkgs\Validation\Concerns\HasFluentRules;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

final class FluentRuleDataset
{
    public function make(): array
    {
        $reflection = new ReflectionClass(HasFluentRules::class);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $dataset = [];

        foreach ($methods as $method) {
            $methodArguments = [];
            $ruleArguments = [];

            foreach ($method->getParameters() as $parameter) {
                $argumentName = $parameter->getName();
                $argumentValue = $this->generateArgumentValue($parameter);

                $ruleArguments[$argumentName] = $argumentValue;

                $parameter->isVariadic()
                    ? $methodArguments = array_merge($methodArguments, $argumentValue)
                    : $methodArguments[] = $argumentValue;

            }

            $methodName = $method->getName();
            $ruleName = $this->resolveRuleName($methodName);

            $dataset[$method->getName()] = [
                'method' => $methodName = $method->getName(),
                'methodArguments' => $methodArguments,
                'rule' => $this->resolveRuleName($methodName),
                'ruleArguments' => $ruleArguments,
            ];
        }

        return $dataset;
    }

    protected function generateArgumentValue(ReflectionParameter $parameter): mixed
    {
        $argumentType = $parameter->getType()?->getName() ?? 'mixed';
        $argumentCount = $parameter->isVariadic() || $argumentType === 'array' ? rand(2,4) : 1;

        return match($argumentType) {
            'int' => $this->generateIntegerArgumentValue($argumentCount),
            'string', 'array' => $this->generateStringArgumentValue($argumentCount),
            'mixed' => $this->generateMixedArgumentValue($argumentCount),
            default => throw new \Exception('Unsupported argument type: ' . $argumentType),
        };
    }

    protected function generateMixedArgumentValue(int $count = 1): mixed
    {
        /** @var \Illuminate\Support\Collection<int, string> $values */
        $values = Collection::times($count, function() {
            return match(Arr::random(['int', 'string'])) {
                'int' => $this->generateIntegerArgumentValue(),
                'string' => $this->generateStringArgumentValue(),
            };
        });

        return $values->count() === 1 ? $values->first() : $values->all();
    }

    protected function generateIntegerArgumentValue(int $count = 1): int|array
    {
        /** @var \Illuminate\Support\Collection<int, string> $values */
        $values = Collection::times($count, fn() => rand(1,100));

        return $values->count() === 1 ? $values->first() : $values->all();
    }

    protected function generateStringArgumentValue(int $count = 1): string|array
    {
        /** @var \Illuminate\Support\Collection<int, string> $values */
        $values = Collection::times($count, fn() => fake()->word());

        return $values->count() === 1 ? $values->first() : $values->all();
    }

    protected function resolveRuleName(string $methodName): string
    {
        return Str::of($methodName)->snake()->toString();
    }
}