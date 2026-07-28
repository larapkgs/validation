<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

final class FormRequestTestController
{
    public function __invoke(TestRequest $request, TestValidation $validatable)
    {
        return response()->json(['status' => 'success', 'data' => $request->validated()]);
    }
}
