<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use LaraPkgs\Validation\Tests\TestSupport\TestsConfigurableComponentGenerator;

uses(TestsConfigurableComponentGenerator::class);

beforeEach(function () {
   $this->initializeTestsConfigurableComponentGenerator('UserValidation', 'validation.generators.validation');
});

afterEach(function () {
    $this->cleanupComponentDirectories();
});

it('creates a new validation class using default configuration values', function (): void {
    expect(File::exists($this->resolveComponentPath()))->toBeFalse();

    expect($this->callComponentMakeCommand())->toBe(0);
    expect(File::exists($this->resolveComponentPath()))->toBeTrue();

    expect(File::get($this->resolveComponentPath()))
        ->toContain('namespace ' . $this->resolveComponentNamespace() . ';')
        ->toContain('final class ' . $this->resolveComponentName() . ' extends Validatable')
        ->toContain('protected function makeValidationCollection(): ValidationCollection');
});

it('creates a new validation class using custom configuration values within the Laravel app path', function (): void {
    $this->setComponentConfig([
        'base_path' => app_path('Domain/Users'),
        'base_namespace' => 'App\\Domain\\Users\\',
        'directory' => 'Rules',
    ]);

    expect(File::exists($this->resolveComponentPath()))->toBeFalse();

    expect($this->callComponentMakeCommand())->toBe(0);
    expect(File::exists($this->resolveComponentPath()))->toBeTrue();

    expect(File::get($this->resolveComponentPath()))
        ->toContain('namespace ' . $this->resolveComponentNamespace() . ';')
        ->toContain('final class ' . $this->resolveComponentName() . ' extends Validatable')
        ->toContain('protected function makeValidationCollection(): ValidationCollection');
});

it('creates a new validation class using custom configuration values outside the Laravel app path', function (): void {
    $this->setComponentConfig([
        'base_path' => base_path('modules'),
        'base_namespace' => 'Modules\\',
    ]);

    expect(File::exists($this->resolveComponentPath()))->toBeFalse();

    expect($this->callComponentMakeCommand())->toBe(0);
    expect(File::exists($this->resolveComponentPath()))->toBeTrue();

    expect(File::get($this->resolveComponentPath()))
        ->toContain('namespace ' . $this->resolveComponentNamespace() . ';')
        ->toContain('final class ' . $this->resolveComponentName() . ' extends Validatable')
        ->toContain('protected function makeValidationCollection(): ValidationCollection');
});

it('normalizes the class name', function (string $nameArgument): void {
    $this->setComponentNameArgument($nameArgument);

    expect(File::exists($this->resolveComponentPath()))->toBeFalse();
    expect($this->callComponentMakeCommand())->toBe(0);

    expect(File::exists($this->resolveComponentPath()))->toBeTrue();

    expect(File::get($this->resolveComponentPath()))
        ->toContain('namespace ' . $this->resolveComponentNamespace() . ';')
        ->toContain('final class ' . $this->resolveComponentName() . ' extends Validatable')
        ->toContain('protected function makeValidationCollection(): ValidationCollection');
})->with([
    'User',
    'User.php',
    'UserValidation.php'
]);

it('accepts a custom path within the root directory', function () {
    $this->setComponentNameArgument('Users/Validation/UserValidation');
    $this->setComponentConfig([
        'base_path' => app_path('Domain'),
        'base_namespace' => 'App\\Domain\\'
    ]);

    expect(File::exists($this->resolveComponentPath()))->toBeFalse();
    expect($this->callComponentMakeCommand())->toBe(0);
    expect(File::exists($this->resolveComponentPath()))->toBeTrue();

    expect(File::get($this->resolveComponentPath()))
        ->toContain('namespace ' . $this->resolveComponentNamespace() . ';')
        ->toContain('final class ' . $this->resolveComponentName() . ' extends Validatable')
        ->toContain('protected function makeValidationCollection(): ValidationCollection');
});

it('fails when trying to create a class that already exists', function (): void {
    expect(File::exists($this->resolveComponentPath()))->toBeFalse();

    expect($this->callComponentMakeCommand())->toBe(0);
    expect($this->callComponentMakeCommand())->toBe(1);

    expect(File::exists($this->resolveComponentPath()))->toBeTrue();
});

it('succeeds when trying to create a class that already exists using the force (--force) option', function (): void {
    expect(File::exists($this->resolveComponentPath()))->toBeFalse();

    expect($this->callComponentMakeCommand())->toBe(0);
    expect(File::exists($this->resolveComponentPath()))->toBeTrue();

    expect($this->callComponentMakeCommand(parameters: ['--force' => true]))->toBe(0);
    expect(File::exists($this->resolveComponentPath()))->toBeTrue();
});

it('uses the published validation.stub when available', function (): void {
    $stubPath = base_path('stubs/validation.stub');
    expect(File::exists($stubPath))->toBeFalse();

    Artisan::call('vendor:publish', ['--tag'   => 'larapkgs-validation-stubs']);
    expect(File::exists($stubPath))->toBeTrue();

    $stubContent = File::get($stubPath);
    $line = "\n// This is a published stub.";

    File::put($stubPath, $stubContent . $line);
    expect(File::get($stubPath))->toContain($line);

    expect($this->callComponentMakeCommand())->toBe(0);
    expect(File::exists($this->resolveComponentPath()))->toBeTrue();

    expect(File::get($this->resolveComponentPath()))
        ->toContain('namespace ' . $this->resolveComponentNamespace() . ';')
        ->toContain('final class ' . $this->resolveComponentName() . ' extends Validatable')
        ->toContain('protected function makeValidationCollection(): ValidationCollection')
        ->toContain($line);
});