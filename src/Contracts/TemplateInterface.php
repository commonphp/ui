<?php

declare(strict_types=1);

namespace CommonPHP\UI\Contracts;

use CommonPHP\UI\ViewData;

interface TemplateInterface
{
    public function name(): string;

    public function path(): ?string;

    public function data(): ViewData;

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function withData(array|ViewData $data): static;

    public function with(string $key, mixed $value): static;
}
