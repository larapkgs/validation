<?php

use Illuminate\Contracts\Support\Arrayable;
use LaraPkgs\Validation\ValidationItem;

it('expects a key on instantiation', function () {
    $validation = new ValidationItem('property');

    expect($validation->getKey())->toBe('property');
});

it('accepts a variadic list of rules on instantiation', function () {
    $validation = new ValidationItem('property', 'required', 'min:10|max:100');

    expect($validation)->getRules()->toBe(['required', 'min:10', 'max:100']);
});

describe('ValidationItem::getKey()', function () {
    it('provides the key', function () {
       $validation = new ValidationItem('property');

       expect($validation->getKey())->toBe('property');
    });
});

describe('ValidationItem::prefix()', function () {
    it('adds a prefix to the key of a new instance', function () {
        $validation = new ValidationItem('property');

        $prefixed = $validation->prefix('collection.*.');

        expect($prefixed)
            ->not->toBe($validation)
            ->getKey()->toBe('collection.*.property');
    });
});

describe('ValidationItem::addRules()', function () {
    it('sets rules', function () {
        $validation = new ValidationItem('property')
            ->addRules('required')
            ->addRules('min:10|max:100');

        expect($validation)->getRules()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidationItem::getRules()', function () {
    it('provides an array of rules compatible with Laravel validation', function () {
        $validation = new ValidationItem('property', 'required', 'min:10|max:100');

        expect($validation)->getRules()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidationItem::addMessages()', function () {
    it('sets messages', function () {
        $validation = new ValidationItem('property')
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->addMessages(['min:10' => 'The :attribute must be 10 characters minimum.']);

        expect($validation)->getMessages()->toBe([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);
    });
});

describe('ValidationItem::getMessages()', function () {
    it('provides an array of messages compatible with Laravel validation', function () {
        $validation = new ValidationItem('property')
            ->addMessages(['required' => 'The :attribute field is required.'])
            ->addMessages(['min:10' => 'The :attribute must be 10 characters minimum.']);

        expect($validation)->getMessages()->toBe([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);
    });
});

describe('ValidationItem::setCustomAttribute()', function () {
    it('sets a custom attribute', function () {
        $validation = new ValidationItem('property')
            ->setCustomAttribute('customAttribute');

        expect($validation)->getCustomAttribute()->toBe('customAttribute');
    });
});

describe('ValidationItem::getCustomAttribute()', function () {
    it('provides the custom attribute', function () {
        $validation = new ValidationItem('property')
            ->setCustomAttribute('customAttribute');

        expect($validation)->getCustomAttribute()->toBe('customAttribute');
    });

    it('defaults to using the key as the  custom attribute', function () {
        $validation = new ValidationItem('property');

        expect($validation)->getCustomAttribute()->toBe('property');
    });
});

describe('ValidationItem::toArray()', function () {
    it('provides an array of rules, messages and attribute compatible with Laravel Validation', function () {
        $validation = new ValidationItem('property')
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
