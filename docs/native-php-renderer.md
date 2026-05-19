# Native PHP Renderer

`NativePhpRenderer` renders PHP template files using output buffering and explicit helper variables.

## Create The Renderer

```php
use CommonPHP\UI\Drivers\NativePhpRenderer;

$renderer = new NativePhpRenderer([
    __DIR__ . '/templates',
]);
```

You can add paths later:

```php
$renderer->addPath(__DIR__ . '/more-templates');
$paths = $renderer->paths();
```

## Template Resolution

The renderer searches:

1. an explicit template path when `Template::file()` or equivalent metadata is used;
2. the raw template name;
3. the dotted-name path form;
4. each configured template directory with those names;
5. the same candidates with `.php` appended when missing.

For example, `pages.profile` can resolve to:

```text
pages.profile
pages/profile
pages.profile.php
pages/profile.php
templates/pages.profile
templates/pages/profile
templates/pages.profile.php
templates/pages/profile.php
```

## Template Variables

Template data is extracted into local variables:

```php
<h1><?= $e($title) ?></h1>
```

The renderer also provides helper variables:

- `$viewData`, the full `ViewData` instance;
- `$renderer`, the active `NativePhpRenderer`;
- `$components`, the active component registry;
- `$component`, a callable for rendering components;
- `$escape` and `$e`, HTML escaping callables.

Helpers cannot be overwritten by user data because extraction uses `EXTR_SKIP`.

## Escaping

Use `$e()` or `$escape()` for HTML text and attribute values:

```php
<span title="<?= $e($title) ?>"><?= $e($label) ?></span>
```

The helper uses `htmlspecialchars()` with UTF-8 and substitute behavior.

## Layouts

Layouts are rendered after the view body. The view body is assigned to the layout's content key.

```php
use CommonPHP\UI\Layout;
use CommonPHP\UI\View;

$view = new View(
    'pages.profile',
    ['name' => 'Ada'],
    new Layout('layouts.app'),
);
```

```php
<!-- templates/layouts/app.php -->
<main>
<?= $content ?>
</main>
```

## Components

```php
<?= $component('badge', ['label' => 'Ready']) ?>
```

If a component is passed as an object instead of a name, it does not need to be registered first.
