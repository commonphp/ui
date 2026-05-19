<?php

declare(strict_types=1);

namespace CommonPHP\UI\Contracts;

use CommonPHP\Runtime\Contracts\DriverInterface;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewData;

interface RendererInterface extends DriverInterface
{
    public function render(View $view): string;

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function renderTemplate(TemplateInterface|string $template, array|ViewData $data = []): string;

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function renderComponent(ComponentInterface|string $component, array|ViewData $data = []): string;
}
