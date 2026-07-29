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
        $generator = $this->makeGenerator();

        return (! $generator->isOverwriting() && $generator->exists())
            ? $this->handleFailure($generator)
            : $this->handleSuccess($generator);
    }

    protected function handleFailure(Generator $generator): int
    {
        $this->components->error(sprintf('File [%s] already exists.', $generator->getPath()));

        return self::FAILURE;
    }

    protected function handleSuccess(Generator $generator): int
    {
        $generator->generate();

        $this->components->info(sprintf('File [%s] created successfully', $generator->getPath()));

        return self::SUCCESS;
    }

    protected function makeGenerator(): Generator
    {
        /** @var string $fileInput */
        $fileInput = $this->argument('name');
        $stubs = $this->getStubs();

        $config = Config::get('validation.generators.validation');
        if ((bool) $this->option('force')) {
            $config['overwrite'] = true;
        }

        return new Generator($fileInput, ...$stubs)->applyConfig($config);
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
