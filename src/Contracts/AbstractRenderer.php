<?php

declare(strict_types=1);

namespace CommonPHP\UI\Contracts;

use CommonPHP\UI\Component;
use CommonPHP\UI\Template;
use CommonPHP\UI\ViewData;

abstract class AbstractRenderer implements RendererInterface
{
    public function getName(): string
    {
        return static::class;
    }

    protected function template(TemplateInterface|string $template): TemplateInterface
    {
        return $template instanceof TemplateInterface ? $template : new Template($template);
    }

    protected function component(ComponentInterface|string $component): ComponentInterface
    {
        return $component instanceof ComponentInterface ? $component : new Component($component);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    protected function data(array|ViewData $data): ViewData
    {
        return ViewData::from($data);
    }

    /**
     * @param array<string, mixed>|ViewData $overrides
     */
    protected function mergedData(ViewData $defaults, array|ViewData $overrides): ViewData
    {
        return (new ViewData($defaults->all()))->merge($overrides);
    }
}
