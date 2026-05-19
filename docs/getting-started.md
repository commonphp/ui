# Getting Started

CommonPHP UI renders a `View` through a renderer driver. The fastest path is `ViewFactory` with the native PHP renderer.

## Create Templates

Create a PHP template:

```php
<!-- templates/pages/home.php -->
<h1><?= $e($title) ?></h1>
<p><?= $e($message) ?></p>
```

Create an optional layout:

```php
<!-- templates/layouts/main.php -->
<!doctype html>
<html>
<body>
<?= $content ?>
</body>
</html>
```

## Render A View

```php
use CommonPHP\UI\ViewFactory;

$ui = new ViewFactory(templatePaths: [__DIR__ . '/templates']);

$html = $ui->render(
    'pages.home',
    [
        'title' => 'Dashboard',
        'message' => 'Welcome back.',
    ],
    'layouts.main',
);
```

Template names can be dotted (`pages.home`) or path-like (`pages/home.php`). Dotted names are converted into directory paths by the native renderer.

## Register A Component

```php
use CommonPHP\UI\Component;

$ui->registerComponent(new Component('badge', 'components.badge', [
    'label' => 'New',
]));
```

Then call it inside a native PHP template:

```php
<?= $component('badge', ['label' => 'Ready']) ?>
```

## Next Steps

- [Usage](usage.md)
- [Templates and layouts](templates-layouts.md)
- [Components](components.md)
- [Native PHP renderer](native-php-renderer.md)
