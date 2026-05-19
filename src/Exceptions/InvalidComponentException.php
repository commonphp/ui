<?php

declare(strict_types=1);

namespace CommonPHP\UI\Exceptions;

class InvalidComponentException extends UIException
{
    public static function invalidName(): self
    {
        return new self('Component names cannot be empty.');
    }

    public static function notFound(string $name): self
    {
        return new self('Component "' . $name . '" is not registered.');
    }
}
