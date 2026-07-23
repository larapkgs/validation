<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\ValidatableBuilder;

it('expects an instance of the RuleFactory and a key on instantiation', function () {
    $ruleFactory = App::make(RuleFactory::class);
    $validatorFactory = App::make(ValidatorFactory::class);
    $validatable = new ValidatableBuilder($ruleFactory, $validatorFactory, 'property');

    expect($validatable->getKey())->toBe('property');
});

describe('applies fluent rules to the underlying RuleCollection', function () {
    beforeEach(function () {
        $this->validatable  = ValidatableBuilder::make('property');
    });

    it('applies rules without any arguments', function () {
        $validatable = $this->validatable->required();

        expect($validatable)->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validatable)
            ->getRules()->toValidatorArgument()->toBe(['required']);
    });

    it('applies rules that only have a single argument', function () {
        $validatable = $this->validatable->min(1);

        expect($validatable)
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validatable)
            ->getRules()->toValidatorArgument()->toBe(['min:1']);
    });

    it('applies rules that have multiple arguments', function () {
        $validatable = $this->validatable->between(1, 10);

        expect($validatable)->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validatable)
            ->getRules()->toValidatorArgument()->toBe(['between:1,10']);
    });

    it('applies rules that only have variadic arguments', function () {
        $validatable = $this->validatable->contains('category1', 'category2');

        expect($validatable)->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validatable)
            ->getRules()->toValidatorArgument()->toBe(['contains:category1,category2']);
    });

    it('applies rules that have positional and variadic arguments', function () {
        $validatable = $this->validatable->requiredIf('category', 'category1', 'category3');

        expect($validatable)->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validatable)
            ->getRules()->toValidatorArgument()->toBe(['required_if:category,category1,category3']);
    });

    it('allows chaining of fluent rules', function () {
        $validatable = $this->validatable->required()->min(10);

        expect($validatable)->not->toBe($this->validatable)
            ->getRules()->toValidatorArgument()->toBe(['required', 'min:10']);
    });
});

describe('ValidatableBuilder::make', function () {
    it('provides a factory method that expects a key', function () {
        $validatable = ValidatableBuilder::make('property');

        expect($validatable)
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->getKey()->toBe('property');
    });
});

describe('ValidatableBuilder::getKey()', function () {
    it('provides the key', function () {
       $validatable = ValidatableBuilder::make('property');

       expect($validatable->getKey())->toBe('property');
    });
});

describe('ValidatableBuilder::prefix()', function () {
    it('applies a prefix to the key and returns a new instance', function () {
        $validatable = ValidatableBuilder::make('property');

        $prefixed = $validatable->prefix('collection.*.');

        expect($prefixed)
            ->not->toBe($validatable)
            ->getKey()->toBe('collection.*.property');
    });
});

describe('ValidatableBuilder::getRules()', function () {
    it('provides a collection of rules compatible with Laravel validation', function () {
        $getRules = function ($subject) {
            return (fn() => $this->rules)->call($subject);
        };

        $validatable = ValidatableBuilder::make('property')->required()->min(10)->max(100);

        expect($validatable->getRules())
            ->not->toBe($getRules($validatable))
            ->toValidatorArgument()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidatableBuilder::addMessages()', function () {
    it('adds an of messages and returns a new instance', function () {
        $validatable = ValidatableBuilder::make('property');

        $updated = $validatable->addMessages([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);

        expect($updated)
            ->not->toBe($validatable)
            ->getMessages()->toBe([
                'required' => 'The :attribute field is required.',
                'min:10' => 'The :attribute must be 10 characters minimum.'
            ]);
    });
});

describe('ValidatableBuilder::getMessages()', function () {
    it('provides an array of messages compatible with Laravel validation', function () {
        $validatable = ValidatableBuilder::make('property')
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->addMessages(['min:10' => 'The :attribute must be 10 characters minimum.']);

        expect($validatable)->getMessages()->toBe([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);
    });
});

describe('ValidatableBuilder::setCustomAttribute()', function () {
    it('sets a custom attribute and returns a new instance', function () {
        $validatable = ValidatableBuilder::make('property');

        $updated = $validatable->setCustomAttribute('customAttribute');

        expect($updated)
            ->not->toBe($validatable)
            ->getCustomAttribute()->toBe('customAttribute');
    });
});

describe('ValidatableBuilder::getCustomAttribute()', function () {
    it('provides the custom attribute', function () {
        $validatable = ValidatableBuilder::make('property')
            ->setCustomAttribute('customAttribute');

        expect($validatable)->getCustomAttribute()->toBe('customAttribute');
    });

    it('defaults to using the key as the  custom attribute', function () {
        $validatable = ValidatableBuilder::make('property');

        expect($validatable)->getCustomAttribute()->toBe('property');
    });
});

describe('ValidatableBuilder::passes()', function () {
    it('indicates if the given data passes the constraints as set by the rules', function () {
        $validatable = ValidatableBuilder::make('property')->required();
        $data = ['property' => 'value'];

        expect($validatable)->passes($data)->toBeTrue();
    });
});

describe('ValidatableBuilder::fails()', function () {
    it('indicates if the given data fails the constraints as set by the rules', function () {
        $validatable = ValidatableBuilder::make('property')->required();
        $data = [];

        expect($validatable)->fails($data)->toBeTrue();
    });
});

describe('ValidatableBuilder::validate()', function () {
    it('tries to validate the given data', function () {
        $validatable = ValidatableBuilder::make('property')->required();
        $data = ['property' => 'value'];

        expect($validatable)->validate($data)->toBe($data);

        $data = [];

        expect(fn() => $validatable->validate($data))
            ->toThrow(ValidationException::class);
    });
});

describe('ValidatableBuilder::toValidatorArguments()', function () {
    it('provides an array of arguments compatible with the Laravel Validator Factory', function () {
        $validatable = ValidatableBuilder::make('property')->required()
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->setCustomAttribute('customProperty');

        $validatorArguments = $validatable->toValidatorArguments();

        expect($validatorArguments)->toBe([
                'rules' => ['property' => ['required']],
                'messages' => ['property.required' => 'The :attribute field is required.'],
                'attributes' => ['property' => 'customProperty']
        ]);
    });
});

describe('ValidatableBuilder::makeValidator()', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        $validatable = ValidatableBuilder::make('property')->required();

        $validator = $validatable->makeValidator([]);

        expect($validator)->toBeInstanceOf(Validator::class);
    });
});