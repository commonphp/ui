<?php

declare(strict_types=1);

namespace CommonPHP\UI\Contracts;

interface LayoutInterface extends TemplateInterface
{
    public function contentKey(): string;

    public function withContentKey(string $key): static;
}
