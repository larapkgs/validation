<?php

namespace LaraPkgs\Validation\Tests\TestSupport;

use Illuminate\Http\Request;

class MethodInjectionTestController
{
    public function __invoke(Request $request, TestValidation $validatable)
    {
        $validated = $validatable->validate($request->all());

        return response()->json(['status' => 'success', 'data' => $validated]);
    }
}
