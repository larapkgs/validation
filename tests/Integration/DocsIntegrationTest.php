<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use LaraPkgs\Validation\Support\Facades\Validatable;
use LaraPkgs\Validation\Validatable as BaseValidatable;
use LaraPkgs\Validation\ValidatableBuilder;
use LaraPkgs\Validation\ValidatableCollection;

describe('Documentation Examples', function () {

    describe('/introduction/gettings-started.md', function () {
        test('single-item', function () {
            $validatable = Validatable::item('property')->required()->numeric()->min(10);

            $data = [];
            $fails = $validatable->fails($data); // true
            expect($fails)->toBeTrue();

            $passes = $validatable->passes($data); // false
            expect($passes)->toBeFalse();

            expect(fn () => $validated = $validatable->validate($data))
                ->toThrow(ValidationException::class); // Throws Illuminate\Validation\ValidationException;

            $data = ['property' => 100];
            $fails = $validatable->fails($data); // false
            expect($fails)->toBeFalse();

            $passes = $validatable->passes($data); // true
            expect($passes)->toBeTrue();

            $validated = $validatable->validate($data); // ['property' => 100];
            expect($validated)->toBe($data);
        });

        test('multiple-items', function () {
            $validatable = Validatable::collection(
                Validatable::item('name')->required()->string(),
                Validatable::item('email')->required()->string()->email(),
            );

            $data = [];
            $fails = $validatable->fails($data); // true
            expect($fails)->toBeTrue();

            $passes = $validatable->passes($data); // false
            expect($passes)->toBeFalse();

            expect(fn () => $validated = $validatable->validate($data))
                ->toThrow(ValidationException::class); // Throws Illuminate\Validation\ValidationException

            $data = ['name' => 'John Doe', 'email' => 'j.doe@unknown.com'];
            $fails = $validatable->fails($data); // false
            expect($fails)->toBeFalse();

            $passes = $validatable->passes($data); // true
            expect($passes)->toBeTrue();

            $validated = $validatable->validate($data); // ['name' => 'John Doe', 'email' => 'j.doe@unknown.com']
            expect($validated)->toBe($data);
        });

        test('validatable-class', function () {
            $validatable = new class extends BaseValidatable
            {
                protected function makeValidatableCollection(): ValidatableCollection
                {
                    return Validatable::collection(
                        Validatable::item('name')->required()->string(),
                        Validatable::item('email')->required()->string()->email(),
                    );
                }
            };

            $data = [];
            $fails = $validatable->fails($data); // true
            expect($fails)->toBeTrue();

            $passes = $validatable->passes($data); // false
            expect($passes)->toBeFalse();

            expect(fn () => $validated = $validatable->validate($data))
                ->toThrow(ValidationException::class); // Throws Illuminate\Validation\ValidationException

            $data = ['name' => 'John Doe', 'email' => 'j.doe@unknown.com'];
            $fails = $validatable->fails($data); // false
            expect($fails)->toBeFalse();

            $passes = $validatable->passes($data); // true
            expect($passes)->toBeTrue();

            $validated = $validatable->validate($data); // ['name' => 'John Doe', 'email' => 'j.doe@unknown.com']
            expect($validated)->toBe($data);
        });
    });

    describe('/usage/validatable-builder.md', function () {
        test('instantiation', function () {
            $validatable = ValidatableBuilder::make('property');

            expect($validatable)->toBeInstanceOf(ValidatableBuilder::class);
        });

        test('add rules: fluent rules', function () {
            $validatable = ValidatableBuilder::make('property')->required();

            expect($validatable)->toBeInstanceOf(ValidatableBuilder::class);
        });

        test('add rules: rule objects', function () {
            $validatable = ValidatableBuilder::make('property')->applyRule(Rule::numeric());

            expect($validatable)->toBeInstanceOf(ValidatableBuilder::class);
        });

        test('add custom messages', function () {
            $validatable = ValidatableBuilder::make('property')->required()
                ->addMessages(['required' => 'Custom required message.']);

            expect($validatable)->toBeInstanceOf(ValidatableBuilder::class);
        });

        test('add custom attributes', function () {
            $validatable = ValidatableBuilder::make('property')->required()
                ->setCustomAttribute('custom');

            expect($validatable)->toBeInstanceOf(ValidatableBuilder::class);
        });

        test('immutability', function () {
            $validatable = ValidatableBuilder::make('property');

            $mutated = $validatable->required();
            $isSame = $validatable === $mutated; // false
            expect($isSame)->toBeFalse();

            $mutated = $validatable->addMessages(['required' => 'Custom required message.']);
            $isSame = $validatable === $mutated; // false
            expect($isSame)->toBeFalse();

            $mutated = $validatable->setCustomAttribute('custom');
            $isSame = $validatable === $mutated; // false
            expect($isSame)->toBeFalse();
        });

        test('validation methods: validate', function () {
            $validatable = ValidatableBuilder::make('property')->required()->numeric()->min(10);

            $data = [];
            expect(fn () => $validated = $validatable->validate($data))
                ->toThrow(ValidationException::class); // Throws Illuminate\Validation\ValidationException;

            $data = ['property' => 100];
            $validated = $validatable->validate($data); // ['property' => 100];
            expect($validated)->toBe($data);
        });

        test('validation methods: fails', function () {
            $validatable = ValidatableBuilder::make('property')->required()->numeric()->min(10);

            $data = [];
            $fails = $validatable->fails($data); // true
            expect($fails)->toBeTrue();

            $data = ['property' => 100];
            $fails = $validatable->fails($data); // false
            expect($fails)->toBeFalse();
        });

        test('validation methods: passes', function () {
            $validatable = ValidatableBuilder::make('property')->required()->numeric()->min(10);

            $data = [];
            $passes = $validatable->passes($data); // false
            expect($passes)->toBeFalse();

            $data = ['property' => 100];
            $passes = $validatable->passes($data); // true
            expect($passes)->toBeTrue();
        });

        test('validation methods: toValidatorArguments', function () {
            $validatable = ValidatableBuilder::make('property')->required()
                ->addMessages(['required' => 'Custom required message.'])
                ->setCustomAttribute('custom');

            $arguments = $validatable->toValidatorArguments();

            $expected = [
                'rules' => [
                    'property' => ['required'],
                ],
                'messages' => [
                    'property.required' => 'Custom required message.',
                ],
                'attributes' => [
                    'property' => 'custom',
                ],
            ];

            expect($arguments)->toBe($expected);
        });

        test('validation methods: makeValidator', function () {
            $validatable = ValidatableBuilder::make('property')->required();

            $data = ['property' => 'value'];
            $validator = $validatable->makeValidator($data); // Illuminate\Contracts\Validation\Validator;

            expect($validator)->toBeInstanceOf(Validator::class);
        });
    });

    describe('/usage/validatable-collection.md', function () {

        test('instantiation', function () {
            $validatable = ValidatableCollection::make(
                ValidatableBuilder::make('name')->required()->string()->max(255),
                ValidatableBuilder::make('email')->required()->string()->email(),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
            );

            expect($validatable)->toBeInstanceOf(ValidatableCollection::class);
        });

        test('add items', function () {

            $validatable = ValidatableCollection::make()->add(
                ValidatableBuilder::make('name')->required()->string()->max(255),
                ValidatableBuilder::make('email')->required()->string()->email(),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
            );

            expect($validatable)->toBeInstanceOf(ValidatableCollection::class);
        });

        test('get items', function () {
            $validatable = ValidatableCollection::make()->add(
                ValidatableBuilder::make('name')->required()->string()->max(255),
                ValidatableBuilder::make('email')->required()->string()->email(),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
            );

            $items = $validatable->getItems(); // Illuminate\Support\Collection

            expect($items)->toBeINstanceOf(Collection::class);
        });

        test('validation methods: validate', function () {

            $validatable = ValidatableCollection::make(
                ValidatableBuilder::make('name')->required()->string()->max(255),
                ValidatableBuilder::make('email')->required()->string()->email(),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
            );

            $data = [];
            expect(fn () => $validated = $validatable->validate($data))
                ->toThrow(ValidationException::class); // Throws Illuminate\Validation\ValidationException;

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $validated = $validatable->validate($data);

            $expected = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
            ];

            expect($validated)->toBe($expected);
        });

        test('validation methods: fails', function () {
            $validatable = ValidatableCollection::make(
                ValidatableBuilder::make('name')->required()->string()->max(255),
                ValidatableBuilder::make('email')->required()->string()->email(),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
            );

            $data = [];
            $fails = $validatable->fails($data); // true
            expect($fails)->toBeTrue();

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $fails = $validatable->fails($data); // false
            expect($fails)->toBeFalse();
        });

        test('validation methods: passes', function () {
            $validatable = ValidatableCollection::make(
                ValidatableBuilder::make('name')->required()->string()->max(255),
                ValidatableBuilder::make('email')->required()->string()->email(),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
            );

            $data = [];
            $passes = $validatable->passes($data); // false
            expect($passes)->toBeFalse();

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $passes = $validatable->passes($data); // true
            expect($passes)->toBeTrue();
        });

        test('validation methods: toValidatorArguments', function () {
            $validatable = ValidatableCollection::make(
                ValidatableBuilder::make('name')->required()->string()->max(255)
                    ->setCustomAttribute('username'),
                ValidatableBuilder::make('email')->required()->string()->email()
                    ->addMessages(['required' => 'We need your email address to set up your account.']),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
                    ->addMessages(['confirmed' => 'The password confirmation does not match.'])
            );

            $arguments = $validatable->toValidatorArguments();

            $expected = [
                'rules' => [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email'],
                    'password' => ['required', 'string', 'min:8', 'confirmed'],
                ],
                'messages' => [
                    'email.required' => 'We need your email address to set up your account.',
                    'password.confirmed' => 'The password confirmation does not match.',
                ],
                'attributes' => [
                    'name' => 'username',
                    'email' => 'email',
                    'password' => 'password',
                ],
            ];

            expect($arguments)->toBe($expected);
        });

        test('validation methods: makeValidator', function () {
            $validatable = ValidatableCollection::make(
                ValidatableBuilder::make('name')->required()->string()->max(255),
                ValidatableBuilder::make('email')->required()->string()->email(),
                ValidatableBuilder::make('password')->required()->string()->min(8)->confirmed()
            );

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $validator = $validatable->makeValidator($data); // Illuminate\Contracts\Validation\Validator;

            expect($validator)->toBeInstanceOf(Validator::class);
        });
    });

    describe('/usage/validatable-classes.md', function () {

        beforeEach(function () {
            $this->validatableClassesHelpers = new class
            {
                public function makeUserRegistrationValidation(): object
                {
                    return new class extends BaseValidatable
                    {
                        protected function makeValidatableCollection(): ValidatableCollection
                        {
                            return ValidatableCollection::make(
                                ValidatableBuilder::make('name')
                                    ->required()
                                    ->string()
                                    ->max(255),

                                ValidatableBuilder::make('email')
                                    ->required()
                                    ->email()
                                    ->addMessages([
                                        'required' => 'We need your email address to set up your account.',
                                    ]),
                                ValidatableBuilder::make('password')
                                    ->required()
                                    ->string()
                                    ->min(8)
                                    ->confirmed()
                                    ->addMessages([
                                        'confirmed' => 'The password confirmation does not match.',
                                    ])
                            );
                        }
                    };
                }
            };
        });

        test('manually', function () {
            expect($this->validatableClassesHelpers->makeUserRegistrationValidation())->toBeInstanceOf(BaseValidatable::class);
        });

        test('validatable collection', function () {
            $validatable = $this->validatableClassesHelpers->makeUserRegistrationValidation();

            $collection = $validatable->getValidatableCollection(); // LaraPkgs\Validation\ValidatableCollection
            expect($collection)->toBeInstanceOf(ValidatableCollection::class);
        });

        test('validation methods: validate', function () {
            $validatable = $this->validatableClassesHelpers->makeUserRegistrationValidation();

            $data = [];
            expect(fn () => $validated = $validatable->validate($data))
                ->toThrow(ValidationException::class); // Throws Illuminate\Validation\ValidationException;

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $validated = $validatable->validate($data);

            $expected = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
            ];

            expect($validated)->toBe($expected);
        });

        test('validation methods: fails', function () {
            $validatable = $this->validatableClassesHelpers->makeUserRegistrationValidation();

            $data = [];
            $fails = $validatable->fails($data); // true
            expect($fails)->toBeTrue();

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $fails = $validatable->fails($data); // false
            expect($fails)->toBeFalse();
        });

        test('validation methods: passes', function () {
            $validatable = $this->validatableClassesHelpers->makeUserRegistrationValidation();

            $data = [];
            $passes = $validatable->passes($data); // false
            expect($passes)->toBeFalse();

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $passes = $validatable->passes($data); // true
            expect($passes)->toBeTrue();
        });

        test('validation methods: makeValidator', function () {
            $validatable = $this->validatableClassesHelpers->makeUserRegistrationValidation();

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'password' => 'XXXXXXXX',
                'password_confirmation' => 'XXXXXXXX',
            ];

            $validator = $validatable->makeValidator($data); // Illuminate\Contracts\Validation\Validator;
            expect($validator)->toBeInstanceOf(Validator::class);
        });
    });

    describe('/usage/support.md', function () {
        test('validatable facade: item', function () {
            $item = Validatable::item('property')->required(); // LaraPkgs\Validation\ValidatableBuilder

            expect($item)->toBeInstanceOf(ValidatableBuilder::class);
        });

        test('validatable facade: collection', function () {
            $collection = Validatable::collection(
                Validatable::item('property')->required()
            ); // LaraPkgs\Validation\ValidatableCollection

            expect($collection)->toBeInstanceOf(ValidatableCollection::class);
        });

        test('validatable helper: item', function () {
            $item = validatable('property')->required(); // LaraPkgs\Validation\ValidatableBuilder

            expect($item)->toBeInstanceOf(ValidatableBuilder::class);
        });

        test('validatable helper: collection', function () {
            $collection = validatable(
                validatable('property')->required()
            ); // LaraPkgs\Validation\ValidatableCollection

            expect($collection)->toBeInstanceOf(ValidatableCollection::class);
        });
    });

    describe('/advanced/error-wrapping.md', function () {
        test('without wrapping', function () {
            $validatable = Validatable::collection(
                Validatable::item('name')->required(),
                Validatable::item('email')->required()
            );

            try {
                $validated = $validatable->validate([]);
            } catch (ValidationException $e) {
                $errors = $e->errors();

                $expected = [
                    'name' => [
                        'The name field is required.',
                    ],
                    'email' => [
                        'The email field is required.',
                    ],
                ];

                expect($errors)->toBe($expected);
            }
        });

        test('with wrapping', function () {
            $validatable = Validatable::collection(
                Validatable::item('name')->required(),
                Validatable::item('email')->required()
            );

            try {
                $validated = $validatable->validate([], 'data.');
            } catch (ValidationException $e) {
                $errors = $e->errors();

                $expected = [
                    'data.name' => [
                        'The name field is required.',
                    ],
                    'data.email' => [
                        'The email field is required.',
                    ],
                ];

                expect($errors)->toBe($expected);
            }
        });
    });

    describe('/advanced/merging.md', function () {
        beforeEach(function () {
            $this->mergingHelpers = new class
            {
                public function makeUserValidation(): object
                {
                    return new class extends BaseValidatable
                    {
                        protected function makeValidatableCollection(): ValidatableCollection
                        {
                            return Validatable::collection(
                                Validatable::item('name')->required()->string(),
                                Validatable::item('email')->required()->email(),
                            );
                        }
                    };
                }

                public function makeAddressValidation(): object
                {
                    return new class extends BaseValidatable
                    {
                        protected function makeValidatableCollection(): ValidatableCollection
                        {
                            return Validatable::collection(
                                Validatable::item('street')->required()->string(),
                                Validatable::item('city')->required()->string(),
                            );
                        }
                    };
                }
            };
        });

        test('example', function () {
            $userValidation = $this->mergingHelpers->makeUserValidation();
            $addressValidation = $this->mergingHelpers->makeAddressValidation();

            $merged = $userValidation->merge($addressValidation);

            $data = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'street' => 'some street',
                'city' => 'somewhere',
            ];

            $validated = $merged->validate($data);

            $expected = [
                'name' => 'John Doe',
                'email' => 'j.doe@unknown.com',
                'street' => 'some street',
                'city' => 'somewhere',
            ];

            expect($validated)->toBe($expected);
        });
    });

    describe('/advanced/prefixing.md', function () {

        beforeEach(function () {
            $this->prefixHelpers = new class
            {
                public function makeOrderValidation(): object
                {
                    return new class extends BaseValidatable
                    {
                        protected function makeValidatableCollection(): ValidatableCollection
                        {
                            return Validatable::collection(
                                Validatable::item('customer_name')
                                    ->required()->string(),
                                Validatable::item('items')
                                    ->required()->array()->min(1),
                            );
                        }
                    };
                }

                public function makeOrderItemValidation(): object
                {
                    return new class extends BaseValidatable
                    {
                        protected function makeValidatableCollection(): ValidatableCollection
                        {
                            return Validatable::collection(
                                Validatable::item('product_id')
                                    ->required()->integer(),
                                Validatable::item('quantity')
                                    ->required()->integer()->min(1),
                                Validatable::item('is_discounted')
                                    ->required()->boolean(),
                                Validatable::item('discount_percentage')
                                    ->nullable()->requiredIf('is_discounted', true)->numeric()->min(0)->max(100)
                                    ->addMessages(['required_if' => 'The :attribute field is required if is_discounted is marked as true.'])

                            );
                        }
                    };
                }

                public function makeMergedAndPrefixedOrderValidation(): object
                {
                    $orderItemValidation = $this->makeOrderItemValidation();

                    return new class($orderItemValidation) extends BaseValidatable
                    {
                        public function __construct(protected BaseValidatable $orderItemValidation) {}

                        protected function makeValidatableCollection(): ValidatableCollection
                        {
                            $collection = Validatable::collection(
                                Validatable::item('customer_name')
                                    ->required()->string(),
                                Validatable::item('items')
                                    ->required()->array()->min(1),
                            );

                            $orderItemValidation = $this->orderItemValidation->prefix('items.*');

                            return $collection->merge($orderItemValidation);
                        }
                    };
                }
            };
        });

        test('deep prefixing', function () {
            $collection = Validatable::collection(
                Validatable::item('is_company')->required()->boolean(),
                Validatable::item('vat_number')->requiredIf('is_company', true)
            );

            $validatorArguments = $collection->toValidatorArguments();

            $expected = [
                'rules' => [
                    'is_company' => ['required', 'boolean'],
                    'vat_number' => ['required_if:is_company,true'],
                ],
                'messages' => [],
                'attributes' => [
                    'is_company' => 'is_company',
                    'vat_number' => 'vat_number',
                ],
            ];

            expect($validatorArguments)->toBe($expected);

            $prefixed = $collection->prefix('billing');

            $validatorArguments = $prefixed->toValidatorArguments();

            $expected = [
                'rules' => [
                    'billing.is_company' => ['required', 'boolean'],
                    'billing.vat_number' => ['required_if:billing.is_company,true'],
                ],
                'messages' => [],
                'attributes' => [
                    'billing.is_company' => 'billing.is_company',
                    'billing.vat_number' => 'billing.vat_number',
                ],
            ];

            expect($validatorArguments)->toBe($expected);
        });

        test('examples: client', function () {
            $orderValidation = $this->prefixHelpers->makeOrderValidation();
            $itemValidation = $this->prefixHelpers->makeOrderItemValidation()->prefix('items.*');
            $validation = $orderValidation->merge($itemValidation);

            $validatorArguments = $validation->getValidatableCollection()->toValidatorArguments();

            $expected = [
                'rules' => [
                    'customer_name' => ['required', 'string'],
                    'items' => ['required', 'array', 'min:1'],
                    'items.*.product_id' => ['required', 'integer'],
                    'items.*.quantity' => ['required', 'integer', 'min:1'],
                    'items.*.is_discounted' => ['required', 'boolean'],
                    'items.*.discount_percentage' => [
                        'nullable',
                        'required_if:items.*.is_discounted,true',
                        'numeric',
                        'min:0',
                        'max:100',
                    ],
                ],
                'messages' => [
                    'items.*.discount_percentage.required_if' => 'The :attribute field is required if is_discounted is marked as true.',
                ],
                'attributes' => [
                    'customer_name' => 'customer_name',
                    'items' => 'items',
                    'items.*.product_id' => 'items.*.product_id',
                    'items.*.quantity' => 'items.*.quantity',
                    'items.*.is_discounted' => 'items.*.is_discounted',
                    'items.*.discount_percentage' => 'items.*.discount_percentage',
                ],
            ];

            expect($validatorArguments)->toBe($expected);

            $data = [
                'customer_name' => 'Jane Doe',
                'items' => [
                    [
                        'product_id' => 10,
                        'quantity' => 2,
                        'is_discounted' => true,
                        'discount_percentage' => 15,
                    ],
                    [
                        'product_id' => 12,
                        'quantity' => 1,
                        'is_discounted' => false,
                        'discount_percentage' => null,
                    ],
                ],
            ];

            $validated = $validation->validate($data);

            $expected = [
                'customer_name' => 'Jane Doe',
                'items' => [
                    [
                        'product_id' => 10,
                        'quantity' => 2,
                        'is_discounted' => true,
                        'discount_percentage' => 15,
                    ],
                    [
                        'product_id' => 12,
                        'quantity' => 1,
                        'is_discounted' => false,
                        'discount_percentage' => null,
                    ],
                ],
            ];

            expect($validated)->toBe($expected);
        });

        test('examples: in class', function () {
            $validatable = $this->prefixHelpers->makeMergedAndPrefixedOrderValidation();

            expect($validatable)->toBeInstanceOf(BaseValidatable::class);
        });
    });
});
