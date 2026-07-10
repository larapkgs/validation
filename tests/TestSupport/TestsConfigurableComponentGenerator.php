<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

trait TestsConfigurableComponentGenerator
{
    protected ?string $componentNameArgument = null;

    protected ?string $componentConfigPath = null;

    protected function initializeTestsConfigurableComponentGenerator(string $name, string $configPath): static
    {
        return $this->setComponentNameArgument($name)->setComponentConfigPath($configPath)->cleanupPublishedStubsDirectory();
    }

    protected function isInitialized(): bool
    {
        return $this->componentNameArgument !== null && $this->componentConfigPath !== null;
    }

    protected function setComponentNameArgument(string $name): static
    {
        $this->cleanupComponentDirectoryIfInitialized();

        $this->componentNameArgument = Str::trim($name, '/');

        return $this->cleanupComponentDirectoryIfInitialized();
    }

    protected function getComponentNameArgument(): string
    {
        return $this->componentNameArgument;
    }

    protected function setComponentConfigPath(string $componentConfigPath): static
    {
        $this->cleanupComponentDirectoryIfInitialized();

        $this->componentConfigPath = Str::trim($componentConfigPath, '.');

        return $this->cleanupComponentDirectoryIfInitialized();
    }

    protected function getComponentConfigPath(string $key): string
    {
        return  $this->componentConfigPath . '.' . $key;
    }

    protected function setComponentConfig(array|string $key, ?string $value = null): static
    {
        $this->cleanupComponentDirectoryIfInitialized();

        $values = Collection::make(is_array($key) ? $key : [$key => $value])
            ->mapWithKeys(fn(string $value, string $key) => [$this->getComponentConfigPath($key) => $value])
            ->all();

        Config::set($values);

        return $this->cleanupComponentDirectoryIfInitialized();
    }

    protected function getComponentConfig(string $key): string
    {
        return Config::get($this->getComponentConfigPath($key));
    }

    protected function getComponentBasePathConfig(): string
    {
        return Str::trim($this->getComponentConfig('base_path'), '/');
    }

    protected function getComponentBaseNamespaceConfig(): string
    {
        return Str::trim($this->getComponentConfig('base_namespace'), '\\');
    }

    protected function getComponentDirectoryConfig(): string
    {
        return Str::trim($this->getComponentConfig('directory'), '/');
    }

    protected function resolveComponentName(): string
    {
        $name = Str::of($this->componentNameArgument)->explode('/')->last();

        return Str::of($name)
            ->trim('/')
            ->replaceEnd('.php', '')
            ->replaceEnd('Validation', '')
            ->append('Validation')
            ->toString();
    }

    protected function resolveComponentNamespace(): string
    {
        $directory = $this->hasCustomDirectory()
            ? Str::of($this->getComponentNameArgument())->explode('/')->slice(0, -1)->join('/')
            : $this->getComponentDirectoryConfig();

        return Str::of($this->getComponentBaseNamespaceConfig())
            ->append('\\', Str::replace('/', '\\', $directory))
            ->toString();
    }

    protected function resolveComponentDirectory(): string
    {
        return Str::of($this->getComponentBasePathConfig())
            ->when($this->hasCustomDirectory(), function(Stringable $string) {
                $directoriesFromNameInput = Str::of($this->getComponentNameArgument())
                    ->explode('/')->slice(0, -1)->join('/');

                return $string->append('/', $directoriesFromNameInput);
            })
            ->when(!$this->hasCustomDirectory(), function(Stringable $string) {
                return $string->append('/', $this->getComponentDirectoryConfig());
            })
            ->toString();
    }

    protected function hasCustomDirectory(): bool
    {
        return Str::contains($this->getComponentNameArgument(), '/');
    }

    protected function resolveComponentPath(): string
    {
        return $this->resolveComponentDirectory() . '/' . $this->resolveComponentName() . '.php';
    }

    protected function cleanupComponentDirectories(): static
    {
        return $this->cleanupComponentDirectory()->cleanupPublishedStubsDirectory();
    }

    protected function cleanupComponentDirectory(): static
    {
        if(File::isDirectory($componentDirectory = $this->resolveComponentDirectory())) {
            File::deleteDirectory($componentDirectory);
        }

        return $this;
    }

    protected function cleanupComponentDirectoryIfInitialized(): static
    {
        return $this->isInitialized()
            ? $this->cleanupComponentDirectory()
            : $this;
    }

    protected function cleanupPublishedStubsDirectory(): static
    {
        if (File::exists($stubsDirectory = base_path('stubs'))) {
            File::deleteDirectory($stubsDirectory);
        }

        return $this;
    }

    protected function callComponentMakeCommand($parameters = []): int
    {
        $parameters = array_merge(['name' => $this->getComponentNameArgument()], $parameters);

        return Artisan::call('make:validation', $parameters);
    }
}