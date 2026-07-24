<?php

declare(strict_types=1);

namespace LaraPkgs\Validation\Exceptions;

use InvalidArgumentException;

class UnparsableRuleException extends InvalidArgumentException
{
    public function __construct(mixed $subject)
    {
        $type = get_debug_type($subject);
        $message = ("Validation rule subject of type [{$type}] could not be parsed.");

        parent::__construct($message);
    }
}
