<?php

declare(strict_types=1);

namespace CommonPHP\UI\Exceptions;

use Throwable;

class RenderException extends UIException
{
    public static function forTemplate(string $template, Throwable $previous): self
    {
        return new self('Unable to render template "' . $template . '": ' . $previous->getMessage(), 0, $previous);
    }
}
