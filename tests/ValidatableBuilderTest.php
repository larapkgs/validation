<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\Contracts\RuleFactory;
use LaraPkgs\Validation\ValidatableBuilder;

it('expects an instance of the RuleFactory and a key on instantiation', function () {
    $ruleFactory = App::make(RuleFactory::class);
    $validatorFactory = App::make(ValidatorFactory::class);
    $validation = new ValidatableBuilder($ruleFactory, $validatorFactory, 'property');

    expect($validation->getKey())->toBe('property');
});

it('accepts a variadic list of rules on instantiation', function () {
    $validation = ValidatableBuilder::make('property', 'required', 'min:10|max:100');

    expect($validation)->getRules()->toValidatorArgument()->toBe(['required', 'min:10', 'max:100']);
});

describe('applies fluent rules to the underlying RuleCollection', function () {
    beforeEach(function () {
        $this->validation  = ValidatableBuilder::make('property');
    });

    it('applies rules without any arguments', function () {
        expect($this->validation)->required()
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validation)
            ->getRules()->toValidatorArgument()->toBe(['required']);
    });

    it('applies rules that only have a single argument', function () {
        expect($this->validation)->min(1)
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validation)
            ->getRules()->toValidatorArgument()->toBe(['min:1']);
    });

    it('applies rules that have multiple arguments', function () {
        expect($this->validation)->between(1, 10)
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validation)
            ->getRules()->toValidatorArgument()->toBe(['between:1,10']);
    });

    it('applies rules that only have variadic arguments', function () {
        expect($this->validation)->contains('category1', 'category2')
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validation)
            ->getRules()->toValidatorArgument()->toBe(['contains:category1,category2']);
    });

    it('applies rules that have positional and variadic arguments', function () {
        expect($this->validation)->requiredIf('category', 'category1', 'category3')
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->not->toBe($this->validation)
            ->getRules()->toValidatorArgument()->toBe(['required_if:category,category1,category3']);
    });

    it('allows chaining of fluent rules', function () {
        expect($this->validation)->required()->min(10)
            ->not->toBe($this->validation)
            ->getRules()->toValidatorArgument()->toBe(['required', 'min:10']);
    });
});

describe('ValidatableBuilder::make', function () {
    it('provides a factory method that expects a key', function () {
        $validation = ValidatableBuilder::make('property');

        expect($validation)
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->getKey()->toBe('property');
    });

    it('provides a factory method that accepts a variadic list of rules', function () {
        $validation = ValidatableBuilder::make('property', 'required', 'min:10|max:100');

        expect($validation)
            ->toBeInstanceOf(ValidatableBuilder::class)
            ->getRules()->toValidatorArgument()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidatableBuilder::getKey()', function () {
    it('provides the key', function () {
       $validation = ValidatableBuilder::make('property');

       expect($validation->getKey())->toBe('property');
    });
});

describe('ValidatableBuilder::prefix()', function () {
    it('applies a prefix to the key and returns a new instance', function () {
        $validation = ValidatableBuilder::make('property');

        $prefixed = $validation->prefix('collection.*.');

        expect($prefixed)
            ->not->toBe($validation)
            ->getKey()->toBe('collection.*.property');
    });
});

describe('ValidatableBuilder::addRules()', function () {
    it('adds a variadic list of rules and returns a new instance', function () {
        $validation = ValidatableBuilder::make('property');

        $updated = $validation->addRules('required', 'min:10|max:100');

        expect($updated)
            ->not->toBe($validation)
            ->getRules()->toValidatorArgument()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidatableBuilder::getRules()', function () {
    it('provides a collection of rules compatible with Laravel validation', function () {
        $getRules = function ($subject) {
            return (fn() => $this->rules)->call($subject);
        };

        $validation = ValidatableBuilder::make('property', 'required', 'min:10|max:100');

        expect($validation->getRules())
            ->not->toBe($getRules($validation))
            ->toValidatorArgument()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidatableBuilder::addMessages()', function () {
    it('adds an of messages and returns a new instance', function () {
        $validation = ValidatableBuilder::make('property');

        $updated = $validation->addMessages([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);

        expect($updated)
            ->not->toBe($validation)
            ->getMessages()->toBe([
                'required' => 'The :attribute field is required.',
                'min:10' => 'The :attribute must be 10 characters minimum.'
            ]);
    });
});

describe('ValidatableBuilder::getMessages()', function () {
    it('provides an array of messages compatible with Laravel validation', function () {
        $validation = ValidatableBuilder::make('property')
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->addMessages(['min:10' => 'The :attribute must be 10 characters minimum.']);

        expect($validation)->getMessages()->toBe([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);
    });
});

describe('ValidatableBuilder::setCustomAttribute()', function () {
    it('sets a custom attribute and returns a new instance', function () {
        $validation = ValidatableBuilder::make('property');

        $updated = $validation->setCustomAttribute('customAttribute');

        expect($updated)
            ->not->toBe($validation)
            ->getCustomAttribute()->toBe('customAttribute');
    });
});

describe('ValidatableBuilder::getCustomAttribute()', function () {
    it('provides the custom attribute', function () {
        $validation = ValidatableBuilder::make('property')
            ->setCustomAttribute('customAttribute');

        expect($validation)->getCustomAttribute()->toBe('customAttribute');
    });

    it('defaults to using the key as the  custom attribute', function () {
        $validation = ValidatableBuilder::make('property');

        expect($validation)->getCustomAttribute()->toBe('property');
    });
});

describe('ValidatableBuilder::passes()', function () {
    it('indicates if the given data passes the constraints as set by the rules', function () {
        $validation = ValidatableBuilder::make('property')->required();
        $data = ['property' => 'value'];

        expect($validation)->passes($data)->toBeTrue();
    });
});

describe('ValidatableBuilder::fails()', function () {
    it('indicates if the given data fails the constraints as set by the rules', function () {
        $validation = ValidatableBuilder::make('property')->required();
        $data = [];

        expect($validation)->fails($data)->toBeTrue();
    });
});

describe('ValidatableBuilder::validate()', function () {
    it('tries to validate the given data', function () {
        $validation = ValidatableBuilder::make('property')->required();
        $data = ['property' => 'value'];

        expect($validation)->validate($data)->toBe($data);

        $data = [];

        expect(fn() => $validation->validate($data))
            ->toThrow(ValidationException::class);
    });
});

describe('ValidatableBuilder::toValidatorArguments()', function () {it('provides an array of arguments compatible with the Laravel Validator Factory', function () {
        $validation = ValidatableBuilder::make('property')->required()
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->setCustomAttribute('customProperty');

        $validatorArguments = $validation->toValidatorArguments();

        expect($validatorArguments)->toBe([
                'rules' => ['property' => ['required']],
                'messages' => ['property.required' => 'The :attribute field is required.'],
                'attributes' => ['property' => 'customProperty']
        ]);
    });
});

describe('ValidatableBuilder::makeValidator()', function () {
    it('provides a factory method that creates a Laravel Validator for the given data', function () {
        $validation = ValidatableBuilder::make('property')->required();

        $validator = $validation->makeValidator([]);

        expect($validator)->toBeInstanceOf(Validator::class);
    });
});