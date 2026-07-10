<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;

final class MakeValidationCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $signature = 'make:validation {name : The name of the validation class} {--force : Overwrite the validation class if it already exists}';

    /**
     * @var string
     */
    protected $description = 'Create a new validation class';

    /**
     * @var string
     */
    protected $type = 'Validation';

    public function handle(): int|bool|null
    {
        if ((! $this->hasOption('force') || ! $this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            $this->components->error($this->type.' already exists.');

            return Command::FAILURE;
        }

        return parent::handle();
    }

    protected function getStub(): string
    {
        $publishedStubPath = $this->laravel->basePath('stubs/validation.stub');

        return file_exists($publishedStubPath)
            ? $publishedStubPath
            : __DIR__ . '/../../stubs/validation.stub';
    }

    protected function getNameInput(): string
    {
        return Str::of(trim($this->argument('name')))
            ->trim('/')
            ->replaceEnd('.php', '')
            ->replaceEnd('Validation', '')
            ->append('Validation')
            ->toString();
    }

    protected function rootNamespace(): string
    {
        return $this->getBaseNamespaceConfig();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        if(Str::contains($this->getNameInput(), '/')) {
            return $rootNamespace;
        }

        return Str::of($this->getDirectoryConfig())
            ->replace('/', '\\')
            ->prepend($rootNamespace, '\\')
            ->toString();
    }

    protected function getPath($name): string
    {
        return Str::of($name)
            ->replaceFirst($this->rootNamespace(), '')
            ->replace('\\', '/')
            ->trim('/')
            ->prepend($this->getBasePathConfig(), '/')
            ->append('.php')
            ->toString();
    }

    protected function getConfig(string $key): array|string
    {
        return Config::get('validation.generators.validation.' . $key);
    }

    protected function getBasePathConfig(): string
    {
        return Str::trim($this->getConfig('base_path'), '/');
    }

    protected function getBaseNamespaceConfig(): string
    {
        return Str::trim($this->getConfig('base_namespace'), '\\');
    }

    protected function getDirectoryConfig(): string
    {
        return Str::trim($this->getConfig('directory'), '/');
    }
}