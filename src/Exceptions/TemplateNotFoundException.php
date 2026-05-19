<?php

declare(strict_types=1);

namespace CommonPHP\UI\Exceptions;

class TemplateNotFoundException extends UIException
{
    /**
     * @param list<string> $paths
     */
    public static function forTemplate(string $template, array $paths = []): self
    {
        $message = 'Template "' . $template . '" was not found.';

        if ($paths !== []) {
            $message .= ' Searched: ' . implode(', ', $paths) . '.';
        }

        return new self($message);
    }
}
