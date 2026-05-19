# Usage

The package has three common usage styles: `ViewFactory`, direct view objects, and renderer contracts.

## ViewFactory

`ViewFactory` is the simplest entry point. It creates views, templates, layouts, components, and delegates rendering to the active renderer.

```php
use CommonPHP\UI\ViewFactory;

$ui = new ViewFactory(templatePaths: [__DIR__ . '/templates']);

$html = $ui->render('pages.profile', [
    'name' => 'Ada',
]);
```

Render with a layout:

```php
$html = $ui->render(
    'pages.profile',
    ['name' => 'Ada'],
    'layouts.app',
);
```

Create objects without rendering immediately:

```php
$view = $ui->view('pages.profile', ['name' => 'Ada'], 'layouts.app');
$template = $ui->template('pages.profile');
$layout = $ui->layout('layouts.app');
$component = $ui->component('badge', 'components.badge');
```

## Direct View Objects

```php
use CommonPHP\UI\View;
use CommonPHP\UI\Layout;

$view = new View(
    'pages.profile',
    ['name' => 'Ada'],
    new Layout('layouts.app'),
);

$changed = $view
    ->with('name', 'Grace')
    ->withTemplate('pages.account')
    ->withoutLayout();
```

`View` and template object `with*` methods return cloned instances. `ViewData` supports both mutable methods and clone-returning `with*` helpers.

## Direct Renderer Use

```php
use CommonPHP\UI\Drivers\NativePhpRenderer;
use CommonPHP\UI\View;

$renderer = new NativePhpRenderer([__DIR__ . '/templates']);

$html = $renderer->render(new View('pages.home', [
    'title' => 'Home',
]));
```

## Driver Integration

`ViewFactory` uses `DriverIntegratorTrait`, so a renderer class can be installed through the runtime driver container.

```php
use CommonPHP\UI\Drivers\NativePhpRenderer;
use CommonPHP\UI\ViewFactory;

$ui = new ViewFactory();

$ui->setDriver(NativePhpRenderer::class, [
    'templatePaths' => [__DIR__ . '/templates'],
]);
```

When a runtime driver is set, it takes precedence over the direct renderer passed to the factory.
