# CommonPHP UI Documentation

CommonPHP UI is the standalone rendering and component package for CommonPHP applications. It defines view objects, template metadata, layouts, reusable components, renderer contracts, and a native PHP template renderer.

The package is intentionally renderer-neutral. Applications can use the built-in PHP renderer, or install a driver such as Twig that implements `RendererInterface`.

## Start Here

- [Getting started](getting-started.md)
- [Usage](usage.md)
- [Package boundaries](package-boundaries.md)

## UI Concepts

- [View data](view-data.md)
- [Templates and layouts](templates-layouts.md)
- [Components](components.md)
- [Renderers](renderers.md)
- [Native PHP renderer](native-php-renderer.md)
- [Error handling](error-handling.md)

## Examples

- [Examples index](examples/index.md)
- [Basic native rendering](examples/basic-native-rendering.md)
- [Components and layouts](examples/components-layouts.md)
- [Custom renderer](examples/custom-renderer.md)

## Development

- [Testing and QA](testing.md)

## Public API Map

Entry points:

- `CommonPHP\UI\ViewFactory`
- `CommonPHP\UI\Drivers\NativePhpRenderer`

View objects:

- `CommonPHP\UI\View`
- `CommonPHP\UI\ViewData`
- `CommonPHP\UI\Template`
- `CommonPHP\UI\Layout`
- `CommonPHP\UI\Component`
- `CommonPHP\UI\ComponentRegistry`

Contracts:

- `CommonPHP\UI\Contracts\RendererInterface`
- `CommonPHP\UI\Contracts\AbstractRenderer`
- `CommonPHP\UI\Contracts\TemplateInterface`
- `CommonPHP\UI\Contracts\LayoutInterface`
- `CommonPHP\UI\Contracts\ComponentInterface`
- `CommonPHP\UI\Contracts\ComponentRegistryInterface`

Exceptions:

- `CommonPHP\UI\Exceptions\UIException`
- `CommonPHP\UI\Exceptions\RenderException`
- `CommonPHP\UI\Exceptions\TemplateNotFoundException`
- `CommonPHP\UI\Exceptions\InvalidComponentException`
- `CommonPHP\UI\Exceptions\RendererDriverException`
