# LaraPkgs / Validation

An immutability-first, strongly typed validation package for Laravel. It replaces standard array-based validation strings with an expressive PHP API, eliminating state leaks in long-running application environments like **Laravel Octane**.

## Why LaraPkgs/Validation?

Standard Laravel validation relies heavily on raw strings and nested arrays. While flexible, this often leads to duplicate rules across endpoints, rigid form requests, and fragile array manipulations when validating nested or repeated data structures.

`LaraPkgs/Validation` transforms Laravel validation into **strongly typed, composable domain objects** with fluent autocompletion, deterministic rule ordering, and deep prefixing.

## Key Features

### Object-Oriented & Fluent
Full IDE auto-completion for over 70 built-in Laravel validation rules. No more guessing string rule names or array syntax.

### Composable & Reusable
Encapsulate validation logic into dedicated `Validatable` classes and merge them effortlessly across your application.

### Deep Prefixing
Automatically prefix nested collections (e.g. `items.*`) while recursively updating relative rule arguments like `required_if`.

### Rule Precedence
Automatically orders rules by logical execution priority (`modifier` -> `circuit` -> `presence` -> `type` -> `constraint`) to prevent unpredictable behavior.

### Dynamic Error Wrapping
Wrap validation error keys on the fly during execution (e.g. `data.name` instead of `name`) without altering your core validatable definitions.

### 100% Immutable
Thread-safe design ensures that every mutation returns a fresh copy, eliminating unintended side effects across your application.

### Native Framework Integration
Use directly in Controllers, Form Requests, or as standalone domain objects that throw standard `ValidationException` errors.

## Before & After

### Before (Standard Laravel)
```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;

$data = [
    'order' => 'ORD-0001',
    'items' => [
        ['product_id' => 1000, 'quantity' => 1]
    ],
];

// Raw strings, no autocompletion
$validator = Validator::make($data, [
    'order' => 'required|string|max:255',
    'items' => 'required|array|min:1',
    'items.*.product_id' => 'required|integer',
    'items.*.quantity' => 'required|integer|min:1',
]);

$validated = $validator->validate();
```

### After (Using LaraPkgs/Validation)
```php
<?php

declare(strict_types=1);

use LaraPkgs\Validation\Support\Facades\Validatable;

// Define modular, reusable validation collections
$orderValidation = Validatable::collection(
    Validatable::item('order')->required()->string()->max(255),
    Validatable::item('items')->required()->array()->min(1)
);

$orderItemValidation = Validatable::collection(
    Validatable::item('product_id')->required()->integer(),
    Validatable::item('quantity')->required()->integer()->min(1)
);

// Prefix for use with arrays
$orderItemValidation = $orderItemValidation->prefix('items.*');

// Merge rules and validate with full IDE autocompletion & type safety
$validatable = $orderValidation->merge($orderItemValidation);

$validated = $validatable->validate([
    'order' => 'ORD-0001',
    'items' => [
        ['product_id' => 1000, 'quantity' => 1]
    ],
]);
```
The same results can be achieved using reusable [Validatable classes](https://larapkgs.github.io/validation-docs/usage/validatable-classes.html).

## Requirements
This package requires:

- PHP 8.4 or higher
- Laravel 12 or higher

## Installation
You can install the package using composer:

```bash
composer require larapkgs/validation
```

## Documentation
All features of the package are fully documented, including working and copyable examples.
The documentation can be found at the dedicated [documentation website](https://larapkgs.github.io/validation-docs/).

## License
`LaraPkgs/Validation` is open-source software licensed under the [MIT license](LICENSE.md).