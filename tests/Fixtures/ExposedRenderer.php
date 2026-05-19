<?php

declare(strict_types=1);

namespace CommonPHP\UI\Tests\Fixtures;

use CommonPHP\UI\Contracts\AbstractRenderer;
use CommonPHP\UI\Contracts\ComponentInterface;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewData;

final class ExposedRenderer extends AbstractRenderer
{
    public function render(View $view): string
    {
        return 'view:' . $view->template()->name();
    }

    public function renderTemplate(TemplateInterface|string $template, array|ViewData $data = []): string
    {
        $template = $this->template($template);
        $payload = $this->mergedData($template->data(), $data);

        return 'template:' . $template->name() . ':' . json_encode($payload->all(), JSON_THROW_ON_ERROR);
    }

    public function renderComponent(ComponentInterface|string $component, array|ViewData $data = []): string
    {
        $component = $this->component($component);

        return 'component:' . $component->componentName();
    }

    public function exposeTemplate(TemplateInterface|string $template): TemplateInterface
    {
        return $this->template($template);
    }

    public function exposeComponent(ComponentInterface|string $component): ComponentInterface
    {
        return $this->component($component);
    }

    public function exposeData(array|ViewData $data): ViewData
    {
        return $this->data($data);
    }

    public function exposeMergedData(ViewData $defaults, array|ViewData $overrides): ViewData
    {
        return $this->mergedData($defaults, $overrides);
    }
}
