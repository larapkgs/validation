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

describe('ValidationItem::rules()', function () {
    it('sets rules', function () {
        $validation = new ValidationItem('property')
            ->rules('required')
            ->rules('min:10|max:100');

        expect($validation)->getRules()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidationItem::getRules()', function () {
    it('provides an array of rules compatible with Laravel validation', function () {
        $validation = new ValidationItem('property', 'required', 'min:10|max:100');

        expect($validation)->getRules()->toBe(['required', 'min:10', 'max:100']);
    });
});

describe('ValidationItem::messages()', function () {
    it('sets messages', function () {
        $validation = new ValidationItem('property')
            ->messages(['required' => 'The :attribute field is required.'])
            ->messages(['min:10' => 'The :attribute must be 10 characters minimum.']);

        expect($validation)->getMessages()->toBe([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);
    });
});

describe('ValidationItem::getMessages()', function () {
    it('provides an array of messages compatible with Laravel validation', function () {
        $validation = new ValidationItem('property')
            ->messages(['required' => 'The :attribute field is required.'])
            ->messages(['min:10' => 'The :attribute must be 10 characters minimum.']);

        expect($validation)->getMessages()->toBe([
            'required' => 'The :attribute field is required.',
            'min:10' => 'The :attribute must be 10 characters minimum.'
        ]);
    });
});

describe('ValidationItem::customAttribute()', function () {
    it('sets a custom attribute', function () {
        $validation = new ValidationItem('property')
            ->customAttribute('customAttribute');

        expect($validation)->getCustomAttribute()->toBe('customAttribute');
    });
});

describe('ValidationItem::getCustomAttribute()', function () {
    it('provides the custom attribute', function () {
        $validation = new ValidationItem('property')
            ->customAttribute('customAttribute');

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
            ->rules(['required', 'min:10', 'max:100'])
            ->messages(['required' => 'The :attribute field is required.'])
            ->customAttribute('custom');

        expect($validation)->toBeInstanceOf(Arrayable::class)
            ->toArray()->toBe([
                'rules' => ['required', 'min:10', 'max:100'],
                'messages' => ['required' => 'The :attribute field is required.'],
                'attribute' => 'custom'
            ]);
    });
});
