<?php

declare(strict_types=1);

namespace CommonPHP\UI;

use CommonPHP\UI\Contracts\LayoutInterface;
use CommonPHP\UI\Exceptions\UIException;

class Layout extends Template implements LayoutInterface
{
    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function __construct(
        string $name,
        array|ViewData $data = [],
        ?string $path = null,
        private string $contentKey = 'content',
    ) {
        parent::__construct($name, $data, $path);
        $this->contentKey = $this->normalizeContentKey($contentKey);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public static function file(string $path, array|ViewData $data = [], ?string $name = null): static
    {
        return new static($name ?? $path, $data, $path);
    }

    public function contentKey(): string
    {
        return $this->contentKey;
    }

    public function withContentKey(string $key): static
    {
        $clone = clone $this;
        $clone->contentKey = $this->normalizeContentKey($key);

        return $clone;
    }

    private function normalizeContentKey(string $key): string
    {
        $key = trim($key);

        if ($key === '') {
            throw new UIException('Layout content keys cannot be empty.');
        }

        return $key;
    }
}
