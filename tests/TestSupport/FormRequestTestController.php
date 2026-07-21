<?php

namespace LaraPkgs\Validation\Tests\TestSupport;

use LaraPkgs\Validation\Tests\TestSupport\TestRequest;

class FormRequestTestController
{
    public function __invoke(TestRequest $request, TestValidation $validatable)
    {
        return response()->json(['status' => 'success', 'data' => $request->validated()]);
    }
}