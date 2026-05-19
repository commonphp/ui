# Renderers

Renderers are UI drivers. They implement `RendererInterface`, which extends runtime's `DriverInterface`.

## Renderer Contract

```php
namespace CommonPHP\UI\Contracts;

interface RendererInterface extends \CommonPHP\Runtime\Contracts\DriverInterface
{
    public function render(\CommonPHP\UI\View $view): string;

    public function renderTemplate(TemplateInterface|string $template, array|\CommonPHP\UI\ViewData $data = []): string;

    public function renderComponent(ComponentInterface|string $component, array|\CommonPHP\UI\ViewData $data = []): string;
}
```

## AbstractRenderer

`AbstractRenderer` provides:

- `getName()` returning `static::class`;
- template normalization for string names;
- component normalization for string names;
- `ViewData` normalization;
- default-data and override-data merging.

Custom renderers can extend it to reduce boilerplate.

## Choosing A Renderer

Use the built-in native renderer:

```php
use CommonPHP\UI\Drivers\NativePhpRenderer;

$renderer = new NativePhpRenderer([__DIR__ . '/templates']);
```

Or pass any `RendererInterface` implementation to `ViewFactory`:

```php
$ui = new ViewFactory($renderer);
```

Or use runtime driver integration:

```php
$ui->setDriver(CustomRenderer::class, [
    'option' => 'value',
]);
```

## Driver Expectations

Renderer drivers should:

- return a string for every successful render operation;
- throw `UIException` subclasses for expected UI failures;
- wrap unexpected engine failures in `RenderException` or renderer-specific exceptions;
- avoid reaching into HTTP, routing, sessions, or application globals.
