<?php

declare(strict_types=1);

use LaraPkgs\Validation\Exceptions\UnparsableRuleException;

it('formats the correct error message based on the given type', function (mixed $subject) {
    $exception = new UnparsableRuleException($subject);
    $type = get_debug_type($subject);

    expect($exception->getMessage())
        ->toBe("Validation rule subject of type [{$type}] could not be parsed.");
})->with([1, true, new stdClass]);
