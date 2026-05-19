<?php

declare(strict_types=1);

namespace CommonPHP\UI;

use CommonPHP\UI\Contracts\LayoutInterface;
use CommonPHP\UI\Contracts\TemplateInterface;

class View
{
    private TemplateInterface $template;

    private ViewData $data;

    private ?LayoutInterface $layout;

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function __construct(
        TemplateInterface|string $template,
        array|ViewData $data = [],
        LayoutInterface|string|null $layout = null,
    ) {
        $this->template = $template instanceof TemplateInterface ? $template : new Template($template);
        $this->data = ViewData::from($data);
        $this->layout = $layout instanceof LayoutInterface || $layout === null ? $layout : new Layout($layout);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public static function make(
        TemplateInterface|string $template,
        array|ViewData $data = [],
        LayoutInterface|string|null $layout = null,
    ): self {
        return new self($template, $data, $layout);
    }

    public function template(): TemplateInterface
    {
        return $this->template;
    }

    public function data(): ViewData
    {
        return $this->data;
    }

    public function layout(): ?LayoutInterface
    {
        return $this->layout;
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
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

    public function withTemplate(TemplateInterface|string $template): static
    {
        $clone = clone $this;
        $clone->template = $template instanceof TemplateInterface ? $template : new Template($template);

        return $clone;
    }

    public function withLayout(LayoutInterface|string|null $layout): static
    {
        $clone = clone $this;
        $clone->layout = $layout instanceof LayoutInterface || $layout === null ? $layout : new Layout($layout);

        return $clone;
    }

    public function withoutLayout(): static
    {
        return $this->withLayout(null);
    }
}
