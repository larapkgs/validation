<?php

use Illuminate\Contracts\Support\Arrayable;
use LaraPkgs\Validation\Rules\RuleFactory;
use LaraPkgs\Validation\ValidationItem;

it('expects an instance of the RuleFactory and a key on instantiation', function () {
    $ruleFactory = new RuleFactory();
    $validation = new ValidationItem($ruleFactory, 'property');

    expect($validation->getKey())->toBe('property');
});

it('accepts a variadic list of rules on instantiation', function () {
    $validation = ValidationItem::make('property', 'required', 'min:10|max:100');

    expect($validation)->getRules()->toBe(['required', 'min:10', 'max:100']);
});

describe('applies fluent rules to the underlying RuleCollection', function () {
    beforeEach(function () {
        $this->validation  = ValidationItem::make('property');
    });

    it('applies rules without any arguments', function () {
        expect($this->validation)->required()
            ->toBeInstanceOf(ValidationItem::class)
            ->not->toBe($this->validation)
            ->getRules()->toBe(['required']);
    });

    it('applies rules that only have a single argument', function () {
        expect($this->validation)->min(1)
            ->toBeInstanceOf(ValidationItem::class)
            ->not->toBe($this->validation)
            ->getRules()->toBe(['min:1']);
    });

    it('applies rules that have multiple arguments', function () {
        expect($this->validation)->between(1, 10)
            ->toBeInstanceOf(ValidationItem::class)
            ->not->toBe($this->validation)
            ->getRules()->toBe(['between:1,10']);
    });

    it('applies rules that only have variadic arguments', function () {
        expect($this->validation)->contains('category1', 'category2')
            ->toBeInstanceOf(ValidationItem::class)
            ->not->toBe($this->validation)
            ->getRules()->toBe(['contains:category1,category2']);
    });

    it('applies rules that have positional and variadic arguments', function () {
        expect($this->validation)->requiredIf('category', 'category1', 'category3')
            ->toBeInstanceOf(ValidationItem::class)
            ->not->toBe($this->validation)
            ->getRules()->toBe(['required_if:category,category1,category3']);
    });

    it('allows chaining of fluent rules', function () {
        expect($this->validation)->required()->min(10)
            ->not->toBe($this->validation)
            ->getRules()->toBe(['required', 'min:10']);
    });
});

describe('ValidationItem::make', function () {
    it('provides a factory method that expects a key', function () {
        $validation = ValidationItem::make('property');

        expect($validation)
            ->toBeInstanceOf(ValidationItem::class)
            ->getKey()->toBe('property');
    });

    it('provides a factory method that accepts a variadic list of rules', function () {
        $validation = ValidationItem::make('property', 'required', 'min:10|max:100');

        expect($validation)
            ->toBeInstanceOf(ValidationItem::class)
            ->getRules()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidationItem::getKey()', function () {
    it('provides the key', function () {
       $validation = ValidationItem::make('property');

       expect($validation->getKey())->toBe('property');
    });
});

describe('ValidationItem::prefix()', function () {
    it('applies a prefix to the key and returns a new instance', function () {
        $validation = ValidationItem::make('property');

        $prefixed = $validation->prefix('collection.*.');

        expect($prefixed)
            ->not->toBe($validation)
            ->getKey()->toBe('collection.*.property');
    });
});

describe('ValidationItem::addRules()', function () {
    it('adds a variadic list of rules and returns a new instance', function () {
        $validation = ValidationItem::make('property');

        $updated = $validation->addRules('required', 'min:10|max:100');

        expect($updated)
            ->not->toBe($validation)
            ->getRules()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidationItem::getRules()', function () {
    it('provides an array of rules compatible with Laravel validation', function () {
        $validation = ValidationItem::make('property', 'required', 'min:10|max:100');

        expect($validation)->getRules()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidationItem::addMessages()', function () {
    it('adds an of messages and returns a new instance', function () {
        $validation = ValidationItem::make('property');

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

describe('ValidationItem::getMessages()', function () {
    it('provides an array of messages compatible with Laravel validation', function () {
        $validation = ValidationItem::make('property')
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->addMessages(['min:10' => 'The :attribute must be 10 characters minimum.']);

        expect($validation)->getMessages()->toBe([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);
    });
});

describe('ValidationItem::setCustomAttribute()', function () {
    it('sets a custom attribute and returns a new instance', function () {
        $validation = ValidationItem::make('property');

        $updated = $validation->setCustomAttribute('customAttribute');

        expect($updated)
            ->not->toBe($validation)
            ->getCustomAttribute()->toBe('customAttribute');
    });
});

describe('ValidationItem::getCustomAttribute()', function () {
    it('provides the custom attribute', function () {
        $validation = ValidationItem::make('property')
            ->setCustomAttribute('customAttribute');

        expect($validation)->getCustomAttribute()->toBe('customAttribute');
    });

    it('defaults to using the key as the  custom attribute', function () {
        $validation = ValidationItem::make('property');

        expect($validation)->getCustomAttribute()->toBe('property');
    });
});

describe('ValidationItem::toArray()', function () {
    it('provides an array of rules, messages and attribute compatible with Laravel Validation', function () {
        $validation = ValidationItem::make('property')
            ->addRules(['required', 'min:10', 'max:100'])
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->setCustomAttribute('custom');

        expect($validation)->toBeInstanceOf(Arrayable::class)
            ->toArray()->toBe([
                'rules' => ['required', 'min:10', 'max:100'],
                'messages' => ['required' => 'The :attribute field is required.'],
                'attribute' => 'custom'
            ]);
    });
});
