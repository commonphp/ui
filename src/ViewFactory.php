<?php

declare(strict_types=1);

namespace CommonPHP\UI;

use CommonPHP\Runtime\Contracts\DriverIntegratorTrait;
use CommonPHP\UI\Contracts\ComponentInterface;
use CommonPHP\UI\Contracts\ComponentRegistryInterface;
use CommonPHP\UI\Contracts\LayoutInterface;
use CommonPHP\UI\Contracts\RendererInterface;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\Drivers\NativePhpRenderer;
use CommonPHP\UI\Exceptions\RendererDriverException;
use Stringable;

class ViewFactory
{
    use DriverIntegratorTrait;

    private RendererInterface $renderer;

    private ComponentRegistryInterface $components;

    /**
     * @param iterable<string|Stringable> $templatePaths
     */
    public function __construct(
        ?RendererInterface $renderer = null,
        ?ComponentRegistryInterface $components = null,
        iterable $templatePaths = [],
    ) {
        $this->enableDrivers(RendererInterface::class);
        $this->components = $components ?? new ComponentRegistry();
        $this->renderer = $renderer ?? new NativePhpRenderer($templatePaths, $this->components);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function view(
        TemplateInterface|string $template,
        array|ViewData $data = [],
        LayoutInterface|string|null $layout = null,
    ): View {
        return new View($template, $data, $layout);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function template(string $name, array|ViewData $data = []): Template
    {
        return new Template($name, $data);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function file(string $path, array|ViewData $data = [], ?string $name = null): Template
    {
        return Template::file($path, $data, $name);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function layout(
        string $name,
        array|ViewData $data = [],
        ?string $path = null,
        string $contentKey = 'content',
    ): Layout {
        return new Layout($name, $data, $path, $contentKey);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function component(string $name, ?string $template = null, array|ViewData $data = [], ?string $path = null): Component
    {
        return new Component($name, $template, $data, $path);
    }

    public function registerComponent(ComponentInterface $component): static
    {
        $this->components->register($component);

        return $this;
    }

    public function components(): ComponentRegistryInterface
    {
        return $this->components;
    }

    public function renderer(): RendererInterface
    {
        if ($this->hasDriver()) {
            $driver = $this->getDriver();

            if (!$driver instanceof RendererInterface) {
                throw RendererDriverException::invalidDriver($driver);
            }

            return $driver;
        }

        return $this->renderer;
    }

    public function useRenderer(RendererInterface $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function render(
        View|TemplateInterface|string $view,
        array|ViewData $data = [],
        LayoutInterface|string|null $layout = null,
    ): string {
        if (!$view instanceof View) {
            $view = $this->view($view, $data, $layout);
        }

        return $this->renderer()->render($view);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function renderTemplate(TemplateInterface|string $template, array|ViewData $data = []): string
    {
        return $this->renderer()->renderTemplate($template, $data);
    }

    /**
     * @param array<string, mixed>|ViewData $data
     */
    public function renderComponent(ComponentInterface|string $component, array|ViewData $data = []): string
    {
        return $this->renderer()->renderComponent($component, $data);
    }
}
