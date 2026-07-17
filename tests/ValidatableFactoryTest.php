<?php

use LaraPkgs\Validation\ValidatableBuilder;
use LaraPkgs\Validation\ValidatableCollection;
use LaraPkgs\Validation\ValidatableFactory;

beforeEach(function () {
    $this->factory = new ValidatableFactory();
});

describe('ValidatableFactory::make', function () {
    it('instantiates a ValidatableBuilder when the first argument is a string', function () {
        expect($this->factory->make('property'))->toBeInstanceOf(ValidatableBuilder::class);
    });

    it('instantiates a ValidatableCollection when the first argument is a ValidatableBuilder', function () {
        expect($this->factory->make(
            ValidatableBuilder::make('property')
        ))->toBeInstanceOf(ValidatableCollection::class);
    });
});

describe('ValidatableFactory::item', function () {
    it('instantiates a ValidatableBuilder', function () {
        expect($this->factory->item('property'))->toBeInstanceOf(ValidatableBuilder::class);
    });
});

describe('ValidatableFactory::collection', function () {
    it('instantiates a ValidatableCollection', function () {
        expect($this->factory->collection())->toBeInstanceOf(ValidatableCollection::class);
    });

    it('accepts a variadic list of ValidationBuilders', function () {
        expect($this->factory->collection(
            ValidatableBuilder::make('property1'),
            ValidatableBuilder::make('property2')
        ))->toBeInstanceOf(ValidatableCollection::class);
    });
});



