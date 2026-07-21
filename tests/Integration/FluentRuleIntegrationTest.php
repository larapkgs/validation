<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

describe('Fluent Rule Integration', function() {

    describe('accepted', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->accepted();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable->fails($data))->toBeTrue();
        })->with(['no', 'off', 0, '0', false, 'false', 'test', 100]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable->passes($data))->toBeTrue();
        })->with(['yes', 'on', 1, '1', true, 'true']);
    });

    describe('acceptedIf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->acceptedIf('referenced', 1);
        });

        dataset('payload', ['no', 'off', 0, '0', false, 'false', 'test', 100]);

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 1];

            expect($this->validatable->fails($data))->toBeTrue();
        })->with('payload');

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 0];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with('payload');
    });

    describe('activeUrl', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->activeUrl();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['not-a-url', 'https://this-domain-does-not-exist-123456789.com']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['http://laravel.com', 'https://github.com']);
    });

    describe('after', function () {
        dataset('afterArgument', ['today', 'referenced']);

        it('fails on invalid data', function (string $argument) {
            $validatable = validatable('property')->after($argument);

            $data = ['property' => Carbon::today(), 'referenced' => Carbon::today()];

            expect($validatable)->fails($data)->toBeTrue();
        })->with('afterArgument');

        it('passes on valid data', function (string $argument) {
            $validatable = validatable('property')->after($argument);

            $data = ['property' => Carbon::today()->addDay(), 'referenced' => Carbon::today()];

            expect($validatable)->passes($data)->toBeTrue();
        })->with('afterArgument');
    });

    describe('afterOrEqual', function () {
        dataset('afterOrEqualArgument', ['today', 'referenced']);

        it('fails on invalid data', function (string $argument) {
            $validatable = validatable('property')->afterOrEqual($argument);

            $data = ['property' => Carbon::today()->subDay(), 'referenced' => Carbon::today()];

            expect($validatable)->fails($data)->toBeTrue();
        })->with('afterOrEqualArgument');

        it('passes on valid data', function (string $argument) {
            $validatable = validatable('property')->afterOrEqual($argument);

            $data = ['property' => Carbon::today(), 'referenced' => Carbon::today()];

            expect($validatable)->passes($data)->toBeTrue();
        })->with('afterOrEqualArgument');
    });

    describe('alpha', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->alpha();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['9', '/', '|', '-']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['a', 'A']);
    });

    describe('alphaDash', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->alphaDash();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['/', '|',]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['a', 'A', '9', '-']);
    });

    describe('alphaNum', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->alphaNum();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['/', '|', '-']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['a', 'A', '9',]);
    });

    describe('array', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->array();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['string', 9, true, false]);

        it('passes on valid data', function () {
            $data = ['property' => []];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('ascii', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->ascii();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['🚀', 'café', 'üñ', '文字列']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['laravel', 'LaraPkgs2026', 'simple-text_123', '']);
    });

    describe('bail', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->bail()->integer()->min(10);
        });

        it('fails and stops at the first error', function ($payload) {
            $data = ['property' => $payload];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->fails($data))->toBeTrue();

            expect($validator->errors()->get('property'))->toHaveCount(1);
        })->with(['abc', true]);

        it('passes on valid data', function () {
            $data = ['property' => 100];

            expect($this->validatable->passes($data))->toBeTrue();
        });
    });

    describe('before', function () {
        dataset('beforeArgument', ['today', 'referenced']);

        it('fails on invalid data', function (string $argument) {
            $validatable = validatable('property')->before($argument);

            $data = ['property' => Carbon::today()->addDay(), 'referenced' => Carbon::today()];

            expect($validatable)->fails($data)->toBeTrue();
        })->with('beforeArgument');

        it('passes on valid data', function (string $argument) {
            $validatable = validatable('property')->before($argument);

            $data = ['property' => Carbon::today()->subDay(), 'referenced' => Carbon::today()];

            expect($validatable)->passes($data)->toBeTrue();
        })->with('beforeArgument');
    });

    describe('beforeOrEqual', function () {
        dataset('beforeOrEqualArgument', ['today', 'referenced']);

        it('fails on invalid data', function (string $argument) {
            $validatable = validatable('property')->beforeOrEqual($argument);

            $data = ['property' => Carbon::today()->addDay(), 'referenced' => Carbon::today()];

            expect($validatable)->fails($data)->toBeTrue();
        })->with('beforeOrEqualArgument');

        it('passes on valid data', function (string $argument) {
            $validatable = validatable('property')->beforeOrEqual($argument);

            $data = ['property' => Carbon::today(), 'referenced' => Carbon::today()];

            expect($validatable)->passes($data)->toBeTrue();
        })->with('beforeOrEqualArgument');
    });

    describe('between (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->integer()->between(2, 4);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([1,5]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([2,3,4]);
    });

    describe('between (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->string()->between(2, 4);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['a', 'abcde']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['ab', 'abc', 'abcd']);
    });

    describe('between (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->array()->between(2, 4);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[[1]], [[1,2,3,4,5]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[[1,2]], [[1,2,3]], [[1,2,3,4]]]);
    });

    describe('boolean', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->boolean();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['test', 100, [[]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([true, false, 1, 0, "1", "0"]);
    });

    describe('confirmed', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->confirmed();
        });

        it('fails on invalid data', function () {
            $data = ['property' => 'value', 'property_confirmation' => 'not value'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property' => 'value', 'property_confirmation' => 'value'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('contains', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->contains('value', 100);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([['value2']], [['value']], [[100]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['value', 100]], [['value', 100, 200, 'value2']]]);
    });

    describe('currentPassword', function () {
        beforeEach(function () {
            $user = new User()->forceFill(['id' => 1, 'password' => Hash::make('secret-password')]);
            $this->actingAs($user);

            $this->validatable = validatable('property')->currentPassword();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable->fails($data))->toBeTrue();
        })->with(['wrong-password', 'SECRET-PASSWORD']);

        it('passes on valid data', function () {
            $data = ['property' => 'secret-password'];

            expect($this->validatable->passes($data))->toBeTrue();
        });
    });

    describe('date', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->date();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['tomorrow', 'today', 123, true]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['01-01-1990', '1990-01-01', '1990-1-1', '1-1-1990']);
    });

    describe('dateEquals', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->dateEquals('1990-01-01');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['1990-01-02', 123, true]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['01-01-1990', '1990-01-01', '1990-1-1', '1-1-1990']);
    });

    describe('dateFormat', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->dateFormat('Y-m-d');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['01-01-1990', '1990-1-1']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['1990-01-01']);
    });

    describe('decimal', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->decimal(2,3);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([100, 100.1, 100.1234]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([100.12, 100.123]);
    });

    describe('declined', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->declined();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable->fails($data))->toBeTrue();
        })->with(['yes', 'on', 1, '1', true, 'true', 'test', 100]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable->passes($data))->toBeTrue();
        })->with(['no', 'off', 0, '0', false, 'false']);
    });

    describe('declinedIf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->declinedIf('referenced', 1);
        });

        dataset('declinedIfPayload', ['yes', 'on', 1, '1', true, 'true', 'test', 100]);

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 1];

            expect($this->validatable->fails($data))->toBeTrue();
        })->with('declinedIfPayload');

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 0];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with('declinedIfPayload');
    });

    describe('different', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->different('referenced');
        });

        dataset('differentPayload', ['matching-string', 100, true]);

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with('differentPayload');

        it('passes on valid data', function ($payload) {
            $data = ['property' => 'value-a', 'referenced' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with('differentPayload');
    });

    describe('digits', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->digits(3);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([12, 1234, '12', '1234']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([123, '789']);
    });

    describe('digitsBetween', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->digitsBetween(2, 4);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([9, 12345, '9', '12345']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([12, 123, 1234, '55', '555', '5555']);
    });

    describe('dimensions', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->dimensions([
                'min_width=100',
                'min_height=100',
                'max_width=500',
                'max_height=500',
            ]);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            'not-a-file',
            UploadedFile::fake()->image('small.jpg', 50, 50),
            UploadedFile::fake()->image('large.jpg', 600, 600),
        ]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            UploadedFile::fake()->image('exact_min.jpg', 100, 100),
            UploadedFile::fake()->image('exact_max.jpg', 500, 500),
            UploadedFile::fake()->image('valid.jpg', 250, 250),
        ]);
    });

    describe('distinct', function () {
        beforeEach(function () {
            $this->validatable = validatable('property.*')->array()->distinct();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[[1, 2, 2]], [['apple', 'banana', 'apple']]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[1, 2, 3], ['apple', 'banana', 'orange']]);
    });

    describe('doesntContain', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->doesntContain('value', 100);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([['value', 100]], [['value']], [[100]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['value2', 101]]]);
    });

    describe('doesntStartWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->doesntStartWith('demo', 'test');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['demo-framework', 'test-string']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['laravel', 'my-test', '123-lw', '']);
    });

    describe('doesntEndWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->doesntEndWith('demo', 'test');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['test-demo', 'project-demo']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['larapkgs', 'demo-app', 'larapkgs2026', '']);
    });

    describe('email', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->email();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['not-an-email', 'john@', '@example.com', 'john user@example.com', 12345, [['user@example.com']]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['user@example.com', 'user@mail.example.com', 'user+tag@example.com', 'first.last@example.com']);
    });

    describe('encoding', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->encoding('UTF-8');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            UploadedFile::fake()->createWithContent('file.txt', mb_convert_encoding('Café', 'ISO-8859-1', 'UTF-8')),
            UploadedFile::fake()->createWithContent('file.txt', "\xC0\xAF"),
        ]);

        it('passes on valid data', function () {
            $file = UploadedFile::fake()->createWithContent('file.txt', mb_convert_encoding('Hello World', 'UTF-8'));
            $data = ['property' => $file];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('endsWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->endsWith('demo', 'test');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['demo-app', 'test-app']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['app-demo', 'app-test', '']);
    });

    enum TestStatus: string
    {
        case ACTIVE = 'active';
        case INACTIVE = 'inactive';
    }

    describe('exclude', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->exclude();
        });

        it('removes the property from validated data', function () {
            $data = ['property' => 'value'];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->not->toHaveKey('property');
        });
    });

    describe('excludeIf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->excludeIf('referenced', 1);
        });

        it('removes the property when condition matches', function () {
            $data = ['property' => 'value', 'referenced' => 1];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->not->toHaveKey('property');
        });

        it('keeps the property when condition does not match', function () {
            $data = ['property' => 'value', 'referenced' => 0];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->toHaveKey('property');
        });
    });

    describe('excludeUnless', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->excludeUnless('referenced', 1);
        });

        it('keeps the property when condition matches', function () {
            $data = ['property' => 'value', 'referenced' => 1];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->toHaveKey('property');
        });

        it('removes the property when condition does not match', function () {
            $data = ['property' => 'value', 'referenced' => 0];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->not->toHaveKey('property');
        });
    });

    describe('excludeWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->excludeWith('referenced');
        });

        it('removes the property when target field is present', function () {
            $data = ['property' => 'value', 'referenced' => 'value'];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->not->toHaveKey('property');
        });

        it('keeps the property when target field is missing', function () {
            $data = ['property' => 'value'];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->toHaveKey('property');
        });
    });

    describe('excludeWithout', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->excludeWithout('referenced');
        });

        it('removes the property when target field is missing', function () {
            $data = ['property' => 'value'];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->not->toHaveKey('property');
        });

        it('keeps the property when target field is present', function () {
            $data = ['property' => 'value', 'referenced' => 'exists'];
            $validator = $this->validatable->makeValidator($data);

            expect($validator->passes())->toBeTrue();
            expect($validator->validated())->toHaveKey('property');
        });
    });

    describe('exists (mocked)', function () {
        it('fails on invalid data', function () {
            $validatable = validatable('property')->exists('users', 'id');

            $presenceVerifier = Mockery::mock(\Illuminate\Validation\PresenceVerifierInterface::class);
            $presenceVerifier->shouldReceive('getCount')
                ->with('users', 'id', 999, null, null, [])
                ->once()
                ->andReturn(0);

            $validator = $validatable->makeValidator(['property' => 999]);
            $validator->setPresenceVerifier($presenceVerifier);

            expect($validator->fails())->toBeTrue();
        });

        it('passes on valid data', function () {
            $validatable = validatable('property')->exists('users', 'id');

            $presenceVerifier = Mockery::mock(\Illuminate\Validation\PresenceVerifierInterface::class);
            $presenceVerifier->shouldReceive('getCount')->with('users', 'id', 1, null, null, [])->once()->andReturn(1);

            $validator = $validatable->makeValidator(['property' => 1]);
            $validator->setPresenceVerifier($presenceVerifier);

            expect($validator->passes())->toBeTrue();
        });
    });

    describe('extensions', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->extensions('jpg', 'png');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            'not-a-file',
            UploadedFile::fake()->create('document.pdf'),
            UploadedFile::fake()->create('image.gif'),
            UploadedFile::fake()->create('script.sh'),
        ]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            UploadedFile::fake()->create('photo.jpg'),
            UploadedFile::fake()->create('photo.JPG'),
            UploadedFile::fake()->create('image.png'),
        ]);
    });

    describe('file', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->file();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['not-a-file', 123, true, ['array'], null]);

        it('passes on valid data', function () {
            $file = UploadedFile::fake()->create('document.pdf', 100);
            $data = ['property' => $file];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('filled', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->filled();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['', null, [[]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['text-value', 123, [[1]]]);
    });

    describe('gt (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->array()->gt('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => ['a', 'b', 'c']];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[[1, 2]], [[1, 2, 3]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => ['a', 'b', 'c']]; // count 3

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[[1, 2, 3, 4]], [[1, 2, 3, 4, 5]]]);
    });

    describe('gt (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->gt('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 10];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([9, 10, '9', '10']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 10];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([11, '12']);
    });

    describe('gt (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->string()->gt('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'ref_field']; // length 9

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', 'abcdefghi']); // lengths 3 and 9

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'ref_field']; // length 9

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['abcdefghij', 'longer-string-here']); // lengths 10+
    });

    describe('gte (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->array()->gte('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => ['a', 'b', 'c']];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[[1, 2]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => ['a', 'b', 'c']];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[[1, 2, 3]], [[1, 2, 3, 4]]]);
    });

    describe('gte (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->gte('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 10];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([9, '9']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 10];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([10, 11, '10', '11']);
    });

    describe('gte (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->string()->gte('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'ref_field'];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', 'abcdefgh']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'ref_field'];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['abcdefghi', 'abcdefghij', 'longer-string-here']);
    });

    describe('hexColor', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->hexColor();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['fff', 'ffffff', '12345', 'gg0000', '#abcde', 'invalid']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['#fff', '#ffffff', '#AABBCC']);
    });

    describe('in (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property.*')->in('draft', 'published', 'archived');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['draft', 'pending']], [['invalid-status']]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['draft']], [['draft', 'published', 'archived']], [[]]]);
    });

    describe('in (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->in('draft', 'published', 'archived');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['pending', 'deleted', 123]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['draft', 'published', 'archived']);
    });

    describe('inArray', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->inArray('referenced.*');
        });

        it('fails on invalid data', function ($payload) {
            $data = [
                'property' => $payload,
                'referenced' => ['apple', 'banana', 'orange']
            ];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['grape', 123]);

        it('passes on valid data', function ($payload) {
            $data = [
                'referenced' => ['apple', 'banana', 'orange'],
                'property' => $payload,
            ];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['apple', 'orange']);
    });

    describe('inArrayKeys', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->inArrayKeys('us', 'ca');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['mx' => 'Mexico']], [['uk' => 'United Kingdom']], [[]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['us' => 'United States']],
            [['ca' => 'Canada']],
            [['us' => 'United States', 'mx' => 'Mexico']]
        ]);
    });


    describe('image', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->image();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            'not-an-image',
            UploadedFile::fake()->create('document.pdf'),
            UploadedFile::fake()->create('notes.txt'),
        ]);

        it('passes on valid  data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            UploadedFile::fake()->image('avatar.jpg'),
            UploadedFile::fake()->image('photo.png'),
            UploadedFile::fake()->image('animation.gif')
        ]);
    });

    describe('integer', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->integer();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', 12.34, '12.34', [[]], null]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([123, 0, -5, '123', '-5']);
    });

    describe('ip', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->ip();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', '256.256.256.256', '123.456.78.9', 'g::1']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['127.0.0.1', '192.168.1.1', '2001:db8::1', '::1']);
    });

    describe('ipv4', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->ipv4();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['2001:db8::1', '::1', 'abc', '256.256.256.256']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['127.0.0.1', '255.255.255.255', '0.0.0.0']);
    });

    describe('ipv6', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->ipv6();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['127.0.0.1', '192.168.1.1', 'abc', 'g::1']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['2001:db8::1', '::1', 'fe80::1']);
    });

    describe('json', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->json();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', '{key: "value"}', '[1, 2, 3,]']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['{"key": "value"}', '[1, 2, 3]', '"just a string"', '123', 'true', 'null']);
    });

    describe('list', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->list();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            [['a' => 'apple', 'b' => 'banana']],
            [[1 => 'apple', 2 => 'banana']],
            [[0 => 'apple', 2 => 'banana']],
            'not-an-array',
            123,
            null
        ]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['apple', 'banana', 'orange']],
            [[0 => 'a', 1 => 'b', 2 => 'c']],
            [[]]
        ]);
    });

    describe('lowercase', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->lowercase();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['Hello', 'WORLD', 'test123A', 'A', '   SpaceS']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['hello', 'world', 'test123!', 'a', '   spaces']);
    });

    describe('lt (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->lt('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = [
                'property' => $payload,
                'referenced' => ['one', 'two', 'three']
            ];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['a', 'b', 'c']], [['a', 'b', 'c', 'd']]]);

        it('passes on valid data', function ($payload) {
            $data = [
                'property' => $payload,
                'referenced' => ['one', 'two', 'three'] // Count 3
            ];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['a']],
            [['a', 'b']],
            [[]]
        ]); // Strictly less than 3 elements
    });

    describe('lt (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->lt('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 50];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([50, 51, 100]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 50];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([49, 0, -10]);
    });

    describe('lt (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->lt('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'abc'];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', 'abcd', 'longer-string']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'abc'];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['a', 'ab', '']);
    });

    describe('lte (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->lte('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = [
                'property' => $payload,
                'referenced' => ['one', 'two', 'three']
            ];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['a', 'b', 'c', 'd']], [['a', 'b', 'c', 'd', 'e']]]);

        it('passes on valid data', function ($payload) {
            $data = [
                'property' => $payload,
                'referenced' => ['one', 'two', 'three']
            ];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['a', 'b', 'c']], [['a', 'b']], [[]]
        ]);
    });

    describe('lte (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->lte('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 50];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([51, 100]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 50];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([50, 49, 0, -10]);
    });

    describe('lte (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->lte('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'abc'];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abcd', 'longer-string']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'abc'];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['abc', 'ab', 'a', '']);
    });

    describe('macAddress', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->macAddress();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['00:1G:2H:3I:4J:5K', '00-11-22-33-44', '00:11:22:33:44:55:66', 'abc']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['01:23:45:67:89:ab', '01-23-45-67-89-ab', '0123.4567.89ab',]);
    });

    describe('max (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->array()->max(3);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['a', 'b', 'c', 'd']], [['a', 'b', 'c', 'd', 'e']]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['a', 'b', 'c']], [['a']], [[]]]);
    });

    describe('max (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->numeric()->max(10);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([11, 10.1, 100]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([10, 9.9, 0, -5]);
    });

    describe('max (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->string()->max(5);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abcdef', 'longer-string']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['abcde', 'a', '']);
    });

    describe('maxDigits', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->maxDigits(3);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([1000, 12345, '9999', 100.55]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([999, 100, 10, 5, '123', '5']);
    });

    describe('mimes', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->mimes('jpg', 'png');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            'not-a-file',
            UploadedFile::fake()->create('document.pdf'),
            UploadedFile::fake()->image('animation.gif'),
        ]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            UploadedFile::fake()->image('photo.jpg'),
            UploadedFile::fake()->image('photo.png'),
        ]);
    });

    describe('mimetypes', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->mimetypes('image/jpeg', 'image/png');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            'not-a-file',
            UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('notes.txt', 100, 'text/plain'),
        ]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => value($payload)];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            UploadedFile::fake()->image('photo.jpg'),
            UploadedFile::fake()->image('photo.png'),
        ]);
    });

    describe('min (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->min(3);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['a', 'b']], [['a']], [[]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['a', 'b', 'c']], [['a', 'b', 'c', 'd']], [['a', 'b', 'c', 'd', 'e']]]);
    });

    describe('min (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->numeric()->min(10);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([9, 9.9, 0, -5]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([10, 10.1, 11, 100]);
    });

    describe('min (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->min(5);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abcd', 'a']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['abcde', 'abcdef', 'longer-string']);
    });

    describe('minDigits', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->minDigits(3);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([99, 5, '12', 0]); // 2 or fewer digits

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([100, 999, 12345, '5555']); // 3 or more digits
    });

    describe('missing', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->missing();
        });

        it('fails on invalid data', function () {
            $data = ['property' => 'value'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property2' => 'value'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('missingIf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->missingIf('referenced', 'active', 'pending');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => 'value', 'referenced' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['active', 'pending']);

        it('passes on valid data', function ($referenced) {
            $data = ['referenced' => $referenced];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['active', 'pending']);
    });

    describe('missingUnless', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->missingUnless('referenced', 'active');
        });

        it('fails on invalid data', function () {
            $data = ['property' => 'value', 'referenced' => 'inactive'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property' => 'value', 'referenced' => 'active'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('missingWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->missingWith('first', 'second');
        });

        it('fails on invalid data', function ($payload) {
            $data = array_merge(['property' => 'value'], $payload);

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['first' => 'present']], [['second' => 'present']], [['first' => 'present', 'second' => 'present']]]);

        it('passes on valid data', function () {
            $data = ['property' => 'value'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('missingWithAll', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->missingWithAll('first', 'second');
        });

        it('fails on invalid data', function () {
            $data = ['property' => 'any-value', 'first' => 'present', 'second' => 'present'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function ($payload) {
            $data = array_merge(['property' => 'any-value'], $payload);

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['first' => 'present']], [[]]]);
    });

    describe('multipleOf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->multipleOf(5);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([6, 9.9, 2, '7', 1]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([5, 10, 0, 25, '15', -5]);
    });

    describe('notIn', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->notIn('admin', 'owner', 'moderator');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['admin', 'owner', 'moderator']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['user', 'guest', 123, 'ADMIN', '']);
    });

    describe('notRegex', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->notRegex('/[0-9]/');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['text123', '123', 'a1']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['text', 'just-letters', 'SPECIAL-chars!', '']);
    });

    describe('nullable', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->nullable()->integer();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', '12.34']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([null, '', 123, '123']);
    });

    describe('numeric', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->numeric();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', '12abc', [[]], null]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([123, 0, -5, 12.34, '123', '-5.67']);
    });

    describe('present', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->present();
        });

        it('fails on invalid data', function () {
            $data = ['other_field' => 'value'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['present-string', 123, null, '', [[]]]);
    });

    describe('presentIf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->presentIf('referenced', 'active');
        });

        it('fails on invalid data', function () {
            $data = ['referenced' => 'active'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property' => 'value', 'referenced' => 'active'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('presentUnless', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->presentUnless('referenced', 'inactive');
        });

        it('fails on invalid data', function () {
            $data = ['referenced' => 'active'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property' => 'value', 'referenced' => 'inactive'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('presentWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->presentWith('first', 'second');
        });

        it('fails on invalid data', function ($payload) {
            $data = $payload;

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['first' => 'value']], [['second' => 'value']], [['first' => 'value', 'second' => 'value']]]);

        it('passes on valid data', function ($payload) {
            $data = $payload;

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['property' => 'value', 'first' => 'value']], [[]]]);
    });

    describe('presentWithAll', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->presentWithAll('first', 'second');
        });

        it('fails on invalid data', function () {
            $data = ['first' => 'value', 'second' => 'value'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function ($payload) {
            $data = $payload;

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['property' => 'value', 'first' => 'value', 'second' => 'value']],
            [['first' => 'value']],
            [[]]
        ]);
    });

    describe('prohibited', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->prohibited();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['present-string', 123, 0]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([null, '']);
    });

    describe('prohibitedIf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->prohibitedIf('referenced', 'active');
        });

        it('fails on invalid data', function () {
            $data = ['property' => 'value', 'referenced' => 'active'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'active'];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([null, '']);
    });

    describe('prohibitedIfAccepted', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->prohibitedIfAccepted('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => 'value', 'referenced' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([true, 1, 'yes', 'on']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => 'any-value', 'referenced' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([false, 0, 'no', 'off', null]);
    });

    describe('prohibitedIfDeclined', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->prohibitedIfDeclined('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => 'value', 'referenced' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([false, 0, 'no', 'off']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => 'any-value', 'referenced' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([true, 1, 'yes', 'on', null]);
    });

    describe('prohibitedUnless', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->prohibitedUnless('referenced', 'active');
        });

        it('fails on invalid data', function () {
            $data = ['property' => 'value', 'referenced' => 'inactive'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property' => 'value', 'referenced' => 'active',];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('prohibits', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->prohibits('secondary', 'tertiary');
        });

        it('fails on invalid data', function ($payload) {
            $data = array_merge(['property' => 'any-value'], $payload);

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            [['secondary' => 'present']],
            [['tertiary' => 'present']],
            [['secondary' => 'present', 'tertiary' => 'present']]
        ]);

        it('passes on valid data', function ($payload) {
            $data = $payload;

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['property' => 'any-value']],
            [['secondary' => 'present', 'tertiary' => 'present']],
            [[]]
        ]);
    });

    describe('regex', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->regex('/^[0-9]+$/');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['product-12', 'product-abcd', 'PRODUCT-123', 'item-123']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([100, '100']);
    });

    describe('required', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->required();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([null, '', ' ', [[]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['string', 0, 123, ['item']]);
    });

    describe('requiredArrayKeys', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredArrayKeys('id', 'name');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['id' => 1]], [['name' => 'John']], ['not-an-array'], [[]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['id' => 1, 'name' => 'John']], [['id' => 1, 'name' => 'John', 'role' => 'admin']]]);
    });

    describe('requiredIf', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredIf('referenced', 'active');
        });

        it('fails on invalid data', function () {
            $data = ['property' => '', 'referenced' => 'active',];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property' => 'value', 'referenced' => 'active'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('requiredIfAccepted', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredIfAccepted('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => '', 'referenced' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([true, 1, 'yes', 'on']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => '', 'referenced' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([false, 0, 'no', 'off']);
    });

    describe('requiredIfDeclined', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredIfDeclined('referenced');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => '', 'referenced' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([false, 0, 'no', 'off']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => '', 'referenced' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([true, 1, 'yes', 'on']);
    });

    describe('requiredUnless', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredUnless('referenced', 'active');
        });

        it('fails on invalid data', function () {
            $data = ['property' => '', 'referenced' => 'inactive'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function () {
            $data = ['property' => '', 'referenced' => 'active'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('requiredWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredWith('first', 'second');
        });

        it('fails on invalid data', function ($payload) {
            $data = array_merge(['property' => ''], $payload);

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            [['first' => 'present']],
            [['second' => 'present']],
            [['first' => 'present', 'second' => 'present']]
        ]);

        it('passes on valid data', function ($data) {
            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['property' => 'filled', 'first' => 'present']],
            [['first' => null, 'second' => null]]
        ]);
    });

    describe('requiredWithAll', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredWithAll('first', 'second');
        });

        it('fails on invalid data', function () {
            $data = ['property' => '', 'first' => 'present', 'second' => 'present'];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function ($data) {
            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['property' => 'filled', 'first' => 'present', 'second' => 'present']],
            [['property' => '', 'first' => 'present']]
        ]);
    });

    describe('requiredWithout', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredWithout('first', 'second');
        });

        it('fails on invalid data', function ($payload) {
            $data = array_merge(['property' => ''], $payload);

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['first' => 'present']], [[]]]);

        it('passes on valid data', function ($data) {
            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['property' => '', 'first' => 'present', 'second' => 'present']],
            [['property' => 'filled', 'first' => 'present']]
        ]);
    });

    describe('requiredWithoutAll', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->requiredWithoutAll('first', 'second');
        });

        it('fails on invalid data', function () {
            $data = ['property' => '', 'first' => null, 'second' => null];

            expect($this->validatable)->fails($data)->toBeTrue();
        });

        it('passes on valid data', function ($data) {
            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            [['property' => '', 'first' => 'present']],
            [['property' => 'filled']]
        ]);
    });

    describe('same', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->same('referenced');
        });

        dataset('samePayload', ['string-value', 100, 12.34, true]);

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => 'different-value'];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with('samePayload');

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload, 'referenced' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with('samePayload');
    });

    describe('size (array)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->array()->size(3);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([[['a', 'b']], [['a', 'b', 'c', 'd']], [[]]]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([[['a', 'b', 'c']], [[1, 2, 3]]]);
    });

    describe('size (numeric)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->numeric()->size(10);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([9, 11, 0, -10, 10.1]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([10, '10']);
    });

    describe('size (string)', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->string()->size(4);
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['abc', 'abcde']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['abcd', '1234', 'test']);
    });

    describe('sometimes', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->sometimes()->integer()->min(10);
        });

        it('fails on invalid data when the property is present', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['not-a-number', 5, 'abc']);

        it('passes on valid data when the property is present', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([10, 20, '15']);

        it('passes when the property is completely missing', function () {
            $data = ['other_field' => 'value'];

            expect($this->validatable)->passes($data)->toBeTrue();
        });
    });

    describe('startsWith', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->startsWith('demo', 'test');
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['my-demo', 'app-test', 'sample']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['demo-app', 'test-app', 'demo', 'test']);
    });

    describe('string', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->string();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([123, 12.34, true, false, [[]], null]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['hello', '123', 'true', '']);
    });

    describe('timezone', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->timezone();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['Invalid/Timezone', 'UTC+5', 'EST', '123', null]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['UTC', 'America/New_York', 'Europe/Amsterdam', 'Asia/Tokyo', 'UTC']);
    });

    describe('ulid', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->ulid();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['01ARZ3NDEKTSV4RRFFQ69G5FA', '01ARZ3NDEKTSV4RRFFQ69G5FA12', '01ARZ3NDEKTSV4RRFFQ69G5FAO', '12345', null]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['01ARZ3NDEKTSV4RRFFQ69G5FAV', '01H1X2Y3Z4A5B6C7D8E9F0G1H2', '7ZZZZZZZZZZZZZZZZZZZZZZZZZ']);
    });

    describe('unique (mocked)', function () {
        it('fails on invalid data', function () {
            $validatable = validatable('property')->unique('users', 'email');

            $presenceVerifier = Mockery::mock(\Illuminate\Validation\PresenceVerifierInterface::class);
            $presenceVerifier->shouldReceive('getCount')
                ->with('users', 'email', 'existing@example.com', null, null, [])
                ->once()
                ->andReturn(1);

            $validator = $validatable->makeValidator(['property' => 'existing@example.com']);
            $validator->setPresenceVerifier($presenceVerifier);

            expect($validator->fails())->toBeTrue();
        });

        it('passes on valid data', function () {
            $validatable = validatable('property')->unique('users', 'email');

            $presenceVerifier = Mockery::mock(\Illuminate\Validation\PresenceVerifierInterface::class);
            $presenceVerifier->shouldReceive('getCount')
                ->with('users', 'email', 'new@example.com', null, null, [])
                ->once()
                ->andReturn(0);

            $validator = $validatable->makeValidator(['property' => 'new@example.com']);
            $validator->setPresenceVerifier($presenceVerifier);

            expect($validator->passes())->toBeTrue();
        });
    });

    describe('uppercase', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->uppercase();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['Hello', 'world', 'test123a', 'a', '   spaces']);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['HELLO', 'WORLD', 'TEST123!', 'A', '   SPACES', '']);
    });

    describe('url', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->url();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with(['not-a-url', 'http://', '://invalid.com', 'ftp://', null]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with(['http://laravel.com', 'https://laravel.com', 'https://github.com/pestphp/pest', 'http://localhost:8000']);
    });

    describe('uuid', function () {
        beforeEach(function () {
            $this->validatable = validatable('property')->uuid();
        });

        it('fails on invalid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->fails($data)->toBeTrue();
        })->with([
            '12345',
            'f47ac10b-58cc-4372-a567-0e02b2c3d47',
            'f47ac10b-58cc-4372-a567-0e02b2c3d4755',
            'g47ac10b-58cc-4372-a567-0e02b2c3d475',
            null,
        ]);

        it('passes on valid data', function ($payload) {
            $data = ['property' => $payload];

            expect($this->validatable)->passes($data)->toBeTrue();
        })->with([
            'a0a2a2d2-0b87-4a18-83f2-2529882be10a',
            'f47ac10b-58cc-4372-a567-0e02b2c3d475',
            '00000000-0000-0000-0000-000000000000',
        ]);
    });
});