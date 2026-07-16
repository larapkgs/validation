<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use LaraPkgs\Validation\Support\Generator;

final class MakeValidationCommand extends Command
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

    public function handle(): int
    {
        return (!($generator = $this->makeGenerator())->isOverwriting() && $generator->exists())
            ? tap(self::FAILURE, fn() => $this->error('Validation class already exists!'))
            : tap(self::SUCCESS, fn() => $generator->generate());
    }

    protected function makeGenerator(): Generator
    {
        $name = $this->argument('name');
        $stubs = $this->getStubs();
        $config = Config::get('validation.generators.validation');

        return new Generator($name, ...$stubs)->applyConfig($config)
            ->type($this->type)->forceType()
            ->overwrite($this->option('force'));
    }

    /**
     * @return array<int, string>
     */
    protected function getStubs(): array
    {
        $publishedStubPath = $this->laravel->basePath('stubs/validation.stub');
        $defaultStubPath = __DIR__ . '/../../stubs/validation.stub';

        return [$publishedStubPath, $defaultStubPath];
    }
}