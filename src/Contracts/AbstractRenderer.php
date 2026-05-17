<?php

declare(strict_types=1);

namespace CommonPHP\UI\Contracts;

class AbstractRenderer implements RendererInterface
{
    public function getName(): string
    {
        return static::class;
    }
}