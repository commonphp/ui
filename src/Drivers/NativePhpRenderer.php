<?php

declare(strict_types=1);

namespace CommonPHP\UI\Drivers;

use CommonPHP\UI\ComponentRegistry;
use CommonPHP\UI\Contracts\AbstractRenderer;
use CommonPHP\UI\Contracts\ComponentInterface;
use CommonPHP\UI\Contracts\ComponentRegistryInterface;
use CommonPHP\UI\Contracts\TemplateInterface;
use CommonPHP\UI\Exceptions\RenderException;
use CommonPHP\UI\Exceptions\TemplateNotFoundException;
use CommonPHP\UI\Exceptions\UIException;
use CommonPHP\UI\View;
use CommonPHP\UI\ViewData;
use Stringable;
use Throwable;

class NativePhpRenderer extends AbstractRenderer
{
    /**
     * @var list<string>
     */
    private array $templatePaths = [];

    private ComponentRegistryInterface $components;

    /**
     * @param iterable<string|Stringable> $templatePaths
     */
    public function __construct(iterable $templatePaths = [], ?ComponentRegistryInterface $components = null)
    {
        $this->components = $components ?? new ComponentRegistry();

        foreach ($templatePaths as $path) {
            $this->addPath($path);
        }
    }

    public function render(View $view): string
    {
        $body = $this->renderTemplate(
            $view->template(),
            $this->mergedData($view->template()->data(), $view->data()),
        );

        $layout = $view->layout();

        if ($layout === null) {
            return $body;
        }

        $layoutData = $this->mergedData($view->data(), $layout->data())
            ->set($layout->contentKey(), $body);

        return $this->renderTemplate($layout, $layoutData);
    }

    public function renderTemplate(TemplateInterface|string $template, array|ViewData $data = []): string
    {
        $template = $this->template($template);
        $payload = $this->mergedData($template->data(), $data);
        $path = $this->resolveTemplatePath($template);

        return $this->includeTemplate($path, $template, $payload);
    }

    public function renderComponent(ComponentInterface|string $component, array|ViewData $data = []): string
    {
        $component = $component instanceof ComponentInterface
            ? $component
            : $this->components->get($component);

        return $this->renderTemplate($component, $this->mergedData($component->data(), $data));
    }

    public function addPath(string|Stringable $path): static
    {
        $path = trim((string) $path);

        if ($path !== '') {
            $this->templatePaths[] = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
            $this->templatePaths = array_values(array_unique($this->templatePaths));
        }

        return $this;
    }

    /**
     * @return list<string>
     */
    public function paths(): array
    {
        return $this->templatePaths;
    }

    public function components(): ComponentRegistryInterface
    {
        return $this->components;
    }

    private function resolveTemplatePath(TemplateInterface $template): string
    {
        $searched = [];

        foreach ($this->candidatePaths($template) as $candidate) {
            $searched[] = $candidate;

            if (is_file($candidate)) {
                return $candidate;
            }
        }

        throw TemplateNotFoundException::forTemplate($template->name(), $searched);
    }

    /**
     * @return list<string>
     */
    private function candidatePaths(TemplateInterface $template): array
    {
        $candidates = [];

        if ($template->path() !== null) {
            $candidates[] = $template->path();
        }

        foreach ($this->templateNames($template->name()) as $name) {
            $candidates[] = $name;

            foreach ($this->templatePaths as $basePath) {
                $candidates[] = $basePath . DIRECTORY_SEPARATOR . $name;
            }
        }

        return array_values(array_unique($candidates));
    }

    /**
     * @return list<string>
     */
    private function templateNames(string $name): array
    {
        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);
        $dotted = str_replace('.', DIRECTORY_SEPARATOR, $normalized);
        $names = [$normalized, $dotted];

        foreach ([$normalized, $dotted] as $candidate) {
            if (!str_ends_with($candidate, '.php')) {
                $names[] = $candidate . '.php';
            }
        }

        return array_values(array_unique($names));
    }

    private function includeTemplate(string $path, TemplateInterface $template, ViewData $viewData): string
    {
        $bufferLevel = ob_get_level();
        $data = $viewData->all();
        $renderer = $this;
        $components = $this->components;
        $component = fn (ComponentInterface|string $component, array|ViewData $data = []): string
            => $this->renderComponent($component, $data);
        $escape = static fn (mixed $value): string
            => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $e = $escape;

        try {
            ob_start();

            (static function () use ($path, $data, $viewData, $renderer, $components, $component, $template, $escape, $e): void {
                extract($data, EXTR_SKIP);

                include $path;
            })();

            return (string) ob_get_clean();
        } catch (Throwable $exception) {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }

            if ($exception instanceof UIException) {
                throw $exception;
            }

            throw RenderException::forTemplate($template->name(), $exception);
        }
    }
}
