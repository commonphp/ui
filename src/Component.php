<?php

declare(strict_types=1);

namespace CommonPHP\UI;

use CommonPHP\UI\Contracts\ComponentInterface;

class Component extends Template implements ComponentInterface
{
    private string $componentName;

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function __construct(string $name, ?string $template = null, array|ViewData $data = [], ?string $path = null)
    {
        $this->componentName = $this->normalizeName($name);

        parent::__construct($template ?? $this->componentName, $data, $path);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public static function fromTemplate(string $name, string $template, array|ViewData $data = []): static
    {
        return new static($name, $template, $data);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public static function file(string $path, array|ViewData $data = [], ?string $name = null): static
    {
        return new static($name ?? $path, $name ?? $path, $data, $path);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public static function fromFile(string $name, string $path, array|ViewData $data = []): static
    {
        return new static($name, $name, $data, $path);
    }

    public function componentName(): string
    {
        return $this->componentName;
    }
}
