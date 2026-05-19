<?php

declare(strict_types=1);

namespace CommonPHP\UI;

use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\Exceptions\UIException;

class Template implements TemplateInterface
{
    protected string $name;

    protected ?string $path;

    protected ViewData $data;

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function __construct(string $name, array|ViewData $data = [], ?string $path = null)
    {
        $this->name = $this->normalizeName($name);
        $this->data = ViewData::from($data);
        $this->path = $this->normalizePath($path);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public static function named(string $name, array|ViewData $data = []): static
    {
        return new static($name, $data);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public static function file(string $path, array|ViewData $data = [], ?string $name = null): static
    {
        return new static($name ?? $path, $data, $path);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function path(): ?string
    {
        return $this->path;
    }

    public function data(): ViewData
    {
        return $this->data;
    }

    public function withData(array|ViewData $data): static
    {
        $clone = clone $this;
        $clone->data = ViewData::from($data);

        return $clone;
    }

    public function with(string $key, mixed $value): static
    {
        $clone = clone $this;
        $clone->data = $clone->data->with($key, $value);

        return $clone;
    }

    protected function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new UIException('Template names cannot be empty.');
        }

        return $name;
    }

    protected function normalizePath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $path = trim($path);

        return $path === '' ? null : str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
