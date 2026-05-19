<?php

declare(strict_types=1);

namespace CommonPHP\UI\Exceptions;

use Throwable;

class RendererDriverException extends UIException
{
    public static function invalidDriver(mixed $driver): self
    {
        return new self('Expected a UI renderer driver, got ' . get_debug_type($driver) . '.');
    }

    public static function forOperation(string $operation, Throwable $previous): self
    {
        return new self('Renderer driver failed during ' . $operation . ': ' . $previous->getMessage(), 0, $previous);
    }
}
