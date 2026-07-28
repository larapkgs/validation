<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Tests\TestSupport;

use Illuminate\Http\Request;

final class MethodInjectionTestController
{
    public function __invoke(Request $request, TestValidation $validatable)
    {
        $validated = $validatable->validate($request->all());

        return response()->json(['status' => 'success', 'data' => $validated]);
    }
}
