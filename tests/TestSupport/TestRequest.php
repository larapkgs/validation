<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

final class TestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function validator(TestValidation $validatable): Validator
    {
        return $validatable->makeValidator($this->validationData());
    }
}
